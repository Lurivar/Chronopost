<?php

namespace ChronopostPickupPoint\Loop;


use ChronopostPickupPoint\Config\ChronopostPickupPointConst;
use Thelia\Core\Template\Element\ArraySearchLoopInterface;
use Thelia\Core\Template\Element\BaseLoop;
use Thelia\Core\Template\Element\LoopResult;
use Thelia\Core\Template\Element\LoopResultRow;
use Thelia\Core\Template\Loop\Argument\ArgumentCollection;
use Thelia\Core\Translation\Translator;

class ChronopostPickupPointCheckRightsLoop extends BaseLoop implements ArraySearchLoopInterface
{
    /**
     * @return ArgumentCollection
     */
    protected function getArgDefinitions()
    {
        return new ArgumentCollection();
    }

    /**
     * @return array
     */
    public function buildArray()
    {
        $ret = array();
        $config = ChronopostPickupPointConst::getConfig();
        if (!is_writable($config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_DIR])) {
            $ret[] = array("ERRMES"=>Translator::getInstance()->trans("Can't write in the label directory"), "ERRFILE"=>$config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_DIR]);
        }
        if (!is_readable($config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_DIR])) {
            $ret[] = array("ERRMES"=>Translator::getInstance()->trans("Can't read the label directory"), "ERRFILE"=>$config[ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_LABEL_DIR]);
        }

        return $ret;
    }

    /**
     * @param LoopResult $loopResult
     * @return LoopResult
     */
    public function parseResults(LoopResult $loopResult)
    {
        foreach ($loopResult->getResultDataCollection() as $arr) {
            $loopResultRow = new LoopResultRow();
            $loopResultRow
                ->set("ERRMES", $arr["ERRMES"])
                ->set("ERRFILE", $arr["ERRFILE"]);
            $loopResult->addRow($loopResultRow);
        }

        return $loopResult;
    }

}