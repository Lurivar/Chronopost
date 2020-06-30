<?php

namespace ChronopostPickupPoint\Loop;


use ChronopostPickupPoint\ChronopostPickupPoint;
use ChronopostPickupPoint\Config\ChronopostPickupPointConst;
use ChronopostPickupPoint\Model\ChronopostPickupPointDeliveryModeQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Element\PropelSearchLoopInterface;
use Thelia\Core\Template\Loop\Argument\Argument;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;

class ChronopostPickupPointDeliveryMode extends BaseLoop implements PropelSearchLoopInterface
{
    /**
     * Unused
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection();
    }

    /**
     * @return ChronopostPickupPointDeliveryModeQuery|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function buildModelCriteria()
    {
        $config = ChronopostPickupPointConst::getConfig();
        $modes = ChronopostPickupPointDeliveryModeQuery::create();

        $enabledDeliveryTypes[] = $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_13_STATUS] ? "01" : "";
        $enabledDeliveryTypes[] = $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_FRESH_DELIVERY_13_STATUS] ? "2R" : "";
        $enabledDeliveryTypes[] = $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_18_STATUS] ? "16" : "";
        $enabledDeliveryTypes[] = $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_13_BAL_STATUS] ? "58" : "";
        $enabledDeliveryTypes[] = $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_CLASSIC_STATUS] ? "44" : "";
        $enabledDeliveryTypes[] = $config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_EXPRESS_STATUS] ? "17" : "";
        /** @TODO Add other delivery types */

        $modes->filterByCode($enabledDeliveryTypes, Criteria::IN);

        return $modes;
    }

    /**
     * @param LoopResult $loopResult
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        /** @var \ChronopostPickupPointPickupPoint\Model\ChronopostPickupPointDeliveryMode $mode */
        foreach ($loopResult->getResultDataCollection() as $mode) {
            $loopResultRow = new LoopResultRow($mode);
            $loopResultRow
                ->set("ID", $mode->getId())
                ->set("TITLE", $mode->getTitle())
                ->set("CODE", $mode->getCode())
                ->set("FREESHIPPING_ACTIVE", $mode->getFreeshippingActive())
                ->set("FREESHIPPING_FROM", $mode->getFreeshippingFrom());
            $loopResult->addRow($loopResultRow);
        }
        return $loopResult;
    }
}