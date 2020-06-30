<?php

namespace ChronopostPickupPoint\EventListeners;


use ChronopostPickupPoint\ChronopostPickupPoint;
use ChronopostPickupPoint\Config\ChronopostPickupPointConst;
use ChronopostPickupPoint\Model\ChronopostPickupPointAddress;
use ChronopostPickupPoint\Model\ChronopostPickupPointAddressQuery;
use ChronopostPickupPoint\Model\ChronopostPickupPointOrder;
use ChronopostPickupPoint\Model\ChronopostPickupPointOrderQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Log\Tlog;
use Thelia\Model\AddressQuery;
use Thelia\Model\Base\OrderAddressQuery;
use Thelia\Model\CountryQuery;
use Thelia\Model\Customer;
use Thelia\Model\Order;
use Thelia\Model\OrderAddress;
use Thelia\Model\OrderQuery;


class SetDeliveryType implements EventSubscriberInterface
{
    /** @var Request */
    protected $request;

    /**
     * SetDeliveryType constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param $id
     * @return bool
     */
    protected function checkModule($id)
    {
        return $id == ChronopostPickupPoint::getModuleId();
    }

    /**
     * @param OrderEvent $orderEvent
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function saveChronopostPickupPointOrder(OrderEvent $orderEvent)
    {
        if ($this->checkModule($orderEvent->getOrder()->getDeliveryModuleId())) {

            $request = $this->getRequest();
            $chronopostOrder = new ChronopostPickupPointOrder();

            $orderId = $orderEvent->getOrder()->getId();

            if ($request->getSession()->get('ChronopostPickupPointDeliveryFresh13') == 1) {
                $chronopostOrder
                    ->setDeliveryType("Fresh13")
                    ->setDeliveryCode("2R");
            } elseif ($request->getSession()->get('ChronopostPickupPointDeliveryChrono13') == 1) {
                $chronopostOrder
                    ->setDeliveryType("Chrono13")
                    ->setDeliveryCode("01");
            } elseif ($request->getSession()->get('ChronopostPickupPointDeliveryChrono18') == 1) {
                $chronopostOrder
                    ->setDeliveryType("Chrono18")
                    ->setDeliveryCode("16");
            } elseif ($request->getSession()->get('ChronopostPickupPointDeliveryChrono13Bal') == 1) {
                $chronopostOrder
                    ->setDeliveryType("Chrono13Bal")
                    ->setDeliveryCode("56");
            } elseif ($request->getSession()->get('ChronopostPickupPointDeliveryChronoClassic') == 1) {
                $chronopostOrder
                    ->setDeliveryType("ChronoClassic")
                    ->setDeliveryCode("44");
            } elseif ($request->getSession()->get('ChronopostPickupPointDeliveryChronoExpress') == 1) {
                $chronopostOrder
                    ->setDeliveryType("ChronoExpress")
                    ->setDeliveryCode("17");
            }
            /** @TODO Add other delivery types */

            $chronopostOrder
                ->setOrderId($orderId)
                ->setLabelDirectory(ChronopostPickupPoint::getConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_DIR))
                ->save();
        }
    }

    /**
     * @param OrderEvent $orderEvent
     * @return null
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function setChronopostPickupPointDeliveryType(OrderEvent $orderEvent)
    {
        if ($this->checkModule($orderEvent->getDeliveryModule())) {
            $request = $this->getRequest();

            /** @TODO Add other delivery types */
            $isChrono13BalEnabled = $request->get('chronopost-chrono13bal');

            $request->getSession()->set('ChronopostAddressId', 0);
            $request->getSession()->set('ChronopostPickupPointDeliveryChrono13Bal', 0);

            $request->getSession()->set('ChronopostAddressId', $orderEvent->getDeliveryAddress());

            if ($isChrono13BalEnabled) {
                $request->getSession()->set('ChronopostPickupPointDeliveryChrono13Bal', 1);
            } else {
                return;
            }
            /** @TODO END */
        }

        return;
    }

    /**
     * @param $countryId
     * @return string
     */
    private function getCountryIso($countryId)
    {
        return CountryQuery::create()->findOneById($countryId)->getIsoalpha2();
    }

    /**
     * @param OrderAddress $address
     * @return string
     */
    private function getContactName(OrderAddress $address)
    {
        return $address->getFirstname() . " " . $address->getLastname();
    }

    /**
     * @param Customer $customer
     * @return string
     * @throws \Propel\Runtime\Exception\PropelException
     */
    private function getChronopostCivility(Customer $customer)
    {
        $civ = $customer->getCustomerTitle()->getId();

        switch ($civ) {
            case 1:
                return 'M';
                break;
            case 2:
                return 'E';
                break;
            case 3:
                return 'L';
                break;
        }

        return 'M';
    }

    /**
     * Write the data to send to the Chronopost API as an array
     *
     * @param Order $order
     * @return mixed
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function writeAPIData(Order $order, $weight = null, $idBox = 1, $skybillRank = 1)
    {
        $config = ChronopostPickupPointConst::getConfig();
        $customer = $order->getCustomer();

        $customerInvoiceAddress = OrderAddressQuery::create()->findPk($order->getInvoiceOrderAddressId());
        $customerDeliveryAddress = OrderAddressQuery::create()->findPk($order->getDeliveryOrderAddressId());

        $phone = $customerDeliveryAddress->getCellphone();

        if (null == $phone) {
            $phone = $customerDeliveryAddress->getPhone();
        }

        if (null === $weight) {
            //$weight = $this->pickingService->getOrderWeight($order->getId());
            $weight = 0;
        }

        $chronopostProductCode = ChronopostPickupPointOrderQuery::create()->filterByOrderId($order->getId())->findOne()->getDeliveryCode();
        $chronopostProductCode = str_pad($chronopostProductCode, 2, "0", STR_PAD_LEFT);

        $name2 = "";
        if ($customerDeliveryAddress->getCompany()) {
            $name2 = $this->getContactName($customerDeliveryAddress);
        }
        $name3 = "";
        if ($customerInvoiceAddress->getCompany()) {
            $name3 = $this->getContactName($customerInvoiceAddress);
        }

        /** START */

        /** HEADER */
        $APIData["headerValue"] = [
            "idEmit" => "CHRFR",
            "accountNumber" => (int)$config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_CODE_CLIENT],
            "subAccount" => "",
        ];

        /** SHIPPER INFORMATIONS */
        $APIData["shipperValue"] = [
            "shipperCivility" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_CIVILITY],
            "shipperName" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_NAME1],
            "shipperName2" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_NAME2],
            "shipperAdress1" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS1],
            "shipperAdress2" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS2],
            "shipperZipCode" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_ZIP],
            "shipperCity" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_CITY],
            "shipperCountry" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_COUNTRY],
            "shipperContactName" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_CONTACT_NAME],
            "shipperEmail" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_MAIL],
            "shipperPhone" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_PHONE],
            "shipperMobilePhone" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_MOBILE_PHONE],
            "shipperPreAlert" => 0, // todo ?
        ];

        /** CUSTOMER INVOICE INFORMATIONS */
        $APIData["customerValue"] = [
            "customerCivility" => $this->getChronopostCivility($customer),
            "customerName" => $customerInvoiceAddress->getCompany(),
            "customerName2" => $name3,
            "customerAdress1" => $customerInvoiceAddress->getAddress1(),
            "customerAdress2" => $customerInvoiceAddress->getAddress2(),
            "customerZipCode" => $customerInvoiceAddress->getZipcode(),
            "customerCity" => $customerInvoiceAddress->getCity(),
            "customerCountry" => $this->getCountryIso($customerInvoiceAddress->getCountryId()),
            "customerContactName" => $this->getContactName($customerInvoiceAddress),
            "customerEmail" => $customer->getEmail(),
            "customerPhone" => $customerInvoiceAddress->getPhone(),
            "customerMobilePhone" => $customerInvoiceAddress->getCellphone(),
            "customerPreAlert" => 0,
            "printAsSender" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_PRINT_AS_CUSTOMER_STATUS],
        ];

        /** CUSTOMER DELIVERY INFORMATIONS */
        $APIData["recipientValue"] = [
            "recipientName" => $customerDeliveryAddress->getCompany(),
            "recipientName2" => $name2,
            "recipientAdress1" => $customerDeliveryAddress->getAddress1(),
            "recipientAdress2" => $customerDeliveryAddress->getAddress2(),
            "recipientZipCode" => $customerDeliveryAddress->getZipcode(),
            "recipientCity" => $customerDeliveryAddress->getCity(),
            "recipientCountry" => $this->getCountryIso($customerDeliveryAddress->getCountryId()),
            "recipientContactName" => $this->getContactName($customerDeliveryAddress),
            "recipientEmail" => $customer->getEmail(),
            "recipientPhone" => $phone,
            "recipientMobilePhone" => $customerDeliveryAddress->getCellphone(),
            "recipientPreAlert" => 0,
        ];

        /** RefValue */
        $APIData["refValue"] = [
            "shipperRef" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_NAME1],
            "recipientRef" => $customer->getId(),
        ];

        /** SKYBILL  (LABEL INFORMATIONS) */
        $APIData["skybillValue"] = [
            "bulkNumber" => $idBox,
            "skybillRank" => $skybillRank,
            "evtCode" => "DC",
            "productCode" => $chronopostProductCode,
            "shipDate" => date('c'),
            "shipHour" => (int)date('G'),
            "weight" => $weight,
            "weightUnit" => "KGM",
            "service" => "0",
            "objectType" => "MAR", //Todo Change according to product ? Is any product a document instead of a marchandise ?
        ];

        /** SKYBILL PARAMETERS */
        $APIData["skybillParamsValue"] = [
            "mode" => $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_TYPE],
        ];

        /** OTHER PARAMETERS */
        $APIData["password"] = $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_PASSWORD];
        $APIData["version"] = "2.0";

        /** EXPIRATION AND SELL-BY DATE (IN CASE OF FRESH PRODUCT) */
        if (in_array($chronopostProductCode, ["2R", "2P", "2Q", "2S", "3X", "3Y", "4V", "4W", "4X"])) {
            $APIData["scheduledValue"] = [
                "expirationDate" => date('c', mktime(0, 0, 0, date('m'), date('d') + (int)$config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_EXPIRATION_DATE], date('Y'))),
                "sellByDate" => date('c'),
            ];
        }

        return $APIData;
    }

    /**
     * Get the label file extension
     *
     * @param $labelType
     * @return string
     */
    private function getLabelExtension($labelType)
    {
        switch ($labelType) {
            case "SPD":
            case "THE":
            case "PDF":
                return ".pdf";
                break;
            case "Z2D":
                return ".zpl";
                break;
        }
        return ".pdf";
    }

    /**
     * Create the Chronopost label
     *
     * @param OrderEvent $orderEvent
     * @return null
     */
    public function createChronopostLabel(OrderEvent $orderEvent)
    {
        $order = $orderEvent->getOrder();
        $boxQuantity = 1;

        if (!$this->checkModule($order->getDeliveryModuleId())) {
            return false;
        }

        try {
            /** Check if order has status paid */
            if ($orderEvent->getStatus() != ChronopostPickupPoint::getConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_TREATMENT_STATUS)) {
                return false;
            }

            $APIDatas = [];

            $reference = $order->getRef();
            $config = ChronopostPickupPointConst::getConfig();

            $log = Tlog::getNewInstance();
            $log->setDestinations("\\Thelia\\Log\\Destination\\TlogDestinationFile");
            $log->setConfig("\\Thelia\\Log\\Destination\\TlogDestinationFile", 0, THELIA_ROOT . "log" . DS . "log-chronopost-pickup-point-pickup-point.txt");

            $log->notice("#CHRONOPOST // L'étiquette de la commande " . $reference . " est en cours de création.");

            /** Get order infos from table */
            $chronopostOrder = ChronopostPickupPointOrderQuery::create()->filterByOrderId($order->getId())->findOne();

            for ($i = 1; $i <= $boxQuantity; $i++) {
                if ($chronopostOrder) {

                    if (1 == $i) {
                        $APIDatas[] = $this->writeAPIData($order, $order->getWeight(), $boxQuantity, $i);
                    } else {
                        $APIDatas[] = $this->writeAPIData($order, $order->getWeight(), $boxQuantity, $i);
                    }

                } else {
                    $log->error("#CHRONOPOST // Impossible de trouver la commande " . $reference . " dans la table des commandes Chronopost.");
                    return null;
                }
            }

            /** Send order informations to the Chronopost API */
            $soapClient = new \SoapClient(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPING_SERVICE_WSDL, array("trace" => 1, "exception" => 1));

            foreach ($APIDatas as $APIData) {

                $response = $soapClient->__soapCall('shippingV3', [$APIData]);

                if (0 != $response->return->errorCode) {
                    throw new \Exception($response->return->errorMessage);
                }

                /** Create the label accordingly */
                $label = $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_DIR] . $response->return->skybillNumber . $this->getLabelExtension($config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_TYPE]);

                if (false === @file_put_contents($label, $response->return->skybill)) {
                    $log->error("L'étiquette n'a pas pu être sauvegardée dans " . $label);
                } else {
                    $log->notice("L'étiquette Chronopost a été sauvegardée en tant que " . $response->return->skybillNumber . $this->getLabelExtension($config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_TYPE]));
                    $chronopostOrder
                        ->setLabelNumber($response->return->skybillNumber . $this->getLabelExtension($config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_TYPE]))
                        ->save();
                }
            }

        } catch (\Exception $e) {
            Tlog::getInstance()->addError("#CHRONOPOST // Error when trying to create the label. Chronopost response : " . $e->getMessage());
        }

        return null;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_SET_DELIVERY_MODULE => array('setChronopostPickupPointDeliveryType', 64),
            TheliaEvents::ORDER_BEFORE_PAYMENT => array('saveChronopostPickupPointOrder', 256),
            TheliaEvents::ORDER_UPDATE_STATUS => array('createChronopostLabel', 257)
        );
    }
}