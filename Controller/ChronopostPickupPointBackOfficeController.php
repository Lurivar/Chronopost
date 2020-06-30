<?php

namespace ChronopostPickupPoint\Controller;


use ChronopostPickupPoint\ChronopostPickupPoint;
use ChronopostPickupPoint\Config\ChronopostPickupPointConst;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;

class ChronopostPickupPointBackOfficeController extends BaseAdminController
{
    /**
     * Render the module config page
     *
     * @return \Thelia\Core\HttpFoundation\Response
     */
    public function viewAction($tab)
    {
        return $this->render(
            'module-configure',
            [
                'module_code' => 'ChronopostPickupPoint',
                'current_tab' => $tab,
            ]
        );
    }

    public function saveLabel()
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], 'ChronopostPickupPoint', AccessManager::UPDATE)) {
            return $response;
        }

        $labelNbr = $this->getRequest()->get("labelNbr");
        $labelDir = $this->getRequest()->get("labelDir");

        $file = $labelDir .'/'. $labelNbr;

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
        } else {
            return $this->viewAction('export');
            // todo : Error message
        }

        return $this->generateSuccessRedirect();
    }

    /**
     * Save configuration form - Chronopost informations
     *
     * @return mixed|null|\Symfony\Component\HttpFoundation\Response|\Thelia\Core\HttpFoundation\Response
     */
    public function saveAction()
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], 'ChronopostPickupPoint', AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm("chronopost_pickup_point_configuration_form");

        try {
            $data = $this->validateForm($form)->getData();

            /** Basic informations */
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_CODE_CLIENT, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_CODE_CLIENT]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_CODE_CLIENT_RELAIS, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_CODE_CLIENT_RELAIS]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_DIR, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_DIR]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_TYPE, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_TYPE]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_PASSWORD, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_PASSWORD]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_TREATMENT_STATUS, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_TREATMENT_STATUS]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_PRINT_AS_CUSTOMER_STATUS, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_PRINT_AS_CUSTOMER_STATUS]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_EXPIRATION_DATE, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_EXPIRATION_DATE]);

            /** Delivery types */
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_FRESH_DELIVERY_13_STATUS, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_FRESH_DELIVERY_13_STATUS]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_13_STATUS, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_13_STATUS]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_18_STATUS, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_18_STATUS]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_13_BAL_STATUS, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_13_BAL_STATUS]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_CLASSIC_STATUS, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_CLASSIC_STATUS]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_EXPRESS_STATUS, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_EXPRESS_STATUS]);
            /** @TODO Add other delivery types here */

        } catch (\Exception $e) {
            $this->setupFormErrorContext(
                Translator::getInstance()->trans(
                    "Error",
                    [],
                    ChronopostPickupPoint::DOMAIN_NAME
                ),
                $e->getMessage(),
                $form
            );

            return $this->viewAction('configure');
        }

        return $this->generateSuccessRedirect($form);
    }

    /**
     * Save configuration form - Shipper informations
     *
     * @return mixed|null|\Symfony\Component\HttpFoundation\Response|\Thelia\Core\HttpFoundation\Response
     */
    public function saveActionShipper()
    {
        if (null !== $response = $this->checkAuth([AdminResources::MODULE], 'ChronopostPickupPoint', AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm("chronopost_pickup_point_configuration_form");

        try {
            $data = $this->validateForm($form)->getData();

            /** Shipper informations */
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_NAME1, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_NAME1]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_NAME2, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_NAME2]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS1, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS1]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS2, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS2]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_COUNTRY, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_COUNTRY]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_CITY, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_CITY]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_ZIP, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_ZIP]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_CIVILITY, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_CIVILITY]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_CONTACT_NAME, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_CONTACT_NAME]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_PHONE, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_PHONE]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_MOBILE_PHONE, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_MOBILE_PHONE]);
            ChronopostPickupPoint::setConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_MAIL, $data[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_SHIPPER_MAIL]);

        } catch (\Exception $e) {
            $this->setupFormErrorContext(
                Translator::getInstance()->trans(
                    "Error",
                    [],
                    ChronopostPickupPoint::DOMAIN_NAME
                ),
                $e->getMessage(),
                $form
            );

            return $this->viewAction('configure');
        }

        return $this->generateSuccessRedirect($form);
    }
}