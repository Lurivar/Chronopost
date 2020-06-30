<?php

namespace ChronopostPickupPoint\Loop;


use ChronopostPickupPoint\Model\ChronopostPickupPointOrder;
use ChronopostPickupPoint\Model\ChronopostPickupPointOrderQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Model\OrderQuery;

/**
 * Class ChronopostPickupPointExportLabelLoop
 * @package ChronopostPickupPoint\Loop
 *
 * @method string getOrderRef
 * @method string getDeliveryType
 * @method string getDeliveryCode
 * @method string getLabelNumber
 * @method string getLabelDirectory
 */
class ChronopostPickupPointExportLabelLoop extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection(
            Argument::createAnyTypeArgument('order_ref'),
            Argument::createAnyTypeArgument('delivery_code'),
            Argument::createAnyTypeArgument('delivery_type'),
            Argument::createAnyTypeArgument('label_number'),
            Argument::createAnyTypeArgument('label_directory')
        );
    }

    /**
     * @return ChronopostPickupPointOrderQuery|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $orderRef       = $this->getOrderRef();
        $deliveryType   = $this->getDeliveryType();
        $deliveryCode   = $this->getDeliveryCode();
        $labelNbr       = $this->getLabelNumber();
        $labelDir       = $this->getLabelDirectory();

        if (!is_null($orderRef)) {
            $orderId = OrderQuery::create()->filterByRef($orderRef)->findOne();
        }

        $chronopostOrder = ChronopostPickupPointOrderQuery::create();

        if (!is_null($orderId)) {
            $chronopostOrder->filterByOrderId($orderId);
        }

        if (!is_null($deliveryType)) {
            $chronopostOrder->filterByDeliveryType($deliveryType);
        }

        if (!is_null($deliveryCode)) {
            $chronopostOrder->filterByDeliveryCode($deliveryCode);
        }

        if (!is_null($labelNbr)) {
            $chronopostOrder->filterByLabelNumber($labelNbr);
        }

        if (!is_null($labelDir)) {
            $chronopostOrder->filterByLabelDirectory($labelDir);
        }

        $chronopostOrder->orderById(Criteria::DESC);

        return $chronopostOrder;
    }

    /**
     * @param LoopResult $loopResult
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var ChronopostPickupPointOrder $chronopostOrder */
        foreach ($loopResult->getResultDataCollection() as $chronopostOrder) {

            /** @var  $loopResultRow */
            $loopResultRow = new LoopResultRow($chronopostOrder);

            $loopResultRow
                ->set("REFERENCE", OrderQuery::create()->filterById($chronopostOrder->getOrderId())->findOne()->getRef())
                ->set("DELIVERY_CODE", $chronopostOrder->getDeliveryCode())
                ->set("DELIVERY_TYPE", $chronopostOrder->getDeliveryType())
                ->set("LABEL_NBR", $chronopostOrder->getLabelNumber())
                ->set("LABEL_DIR", $chronopostOrder->getLabelDirectory())
                ->set("ORDER_ID", $chronopostOrder->getOrderId())
                ;
            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }
}