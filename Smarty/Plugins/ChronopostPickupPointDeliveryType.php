<?php

namespace ChronopostPickupPoint\Smarty\Plugins;


use ChronopostPickupPoint\ChronopostPickupPoint;
use ChronopostPickupPoint\Config\ChronopostPickupPointConst;
use Propel\Runtime\Exception\PropelException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\CountryQuery;
use Thelia\Model\Coupon;
use Thelia\Model\CouponQuery;
use Thelia\Module\Exception\DeliveryException;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\SmartyPluginDescriptor;

class ChronopostPickupPointDeliveryType extends AbstractSmartyPlugin
{
    protected $request;
    protected $dispatcher;

    /**
     * ChronopostPickupPointDeliveryType constructor.
     *
     * @param Request $request
     * @param EventDispatcherInterface|null $dispatcher
     */
    public function __construct(Request $request, EventDispatcherInterface $dispatcher = null)
    {
        $this->request = $request;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return array|SmartyPluginDescriptor[]
     */
    public function getPluginDescriptors()
    {
        return array(
            new SmartyPluginDescriptor("function", "chronopostPickupPointDeliveryType", $this, "chronopostPickupPointDeliveryType"),
            new SmartyPluginDescriptor("function", "chronopostPickupPointDeliveryPrice", $this, "chronopostPickupPointDeliveryPrice"),
        );
    }

    /**
     * @param $params
     * @param $smarty
     * @throws PropelException
     */
    public function chronopostPickupPointDeliveryPrice($params, $smarty)
    {
        $deliveryMode = $params["delivery-mode"];
        $country = CountryQuery::create()->findOneById($params["country"]);

        $cartWeight = $this->request->getSession()->getSessionCart($this->dispatcher)->getWeight();
        $cartAmount = $this->request->getSession()->getSessionCart($this->dispatcher)->getTaxedAmount($country);

        try {

            $price = ChronopostPickupPoint::getPostageAmount(
                $country->getAreaId(),
                $cartWeight,
                $cartAmount,
                $deliveryMode
            );

            $consumedCouponsCodes = $this->request->getSession()->getConsumedCoupons();

            foreach ($consumedCouponsCodes as $consumedCouponCode)  {
                $coupon = CouponQuery::create()
                    ->filterByCode($consumedCouponCode)
                    ->findOne();

                /** @var Coupon $coupon */
                if(null  !== $coupon){
                    if($coupon->getIsRemovingPostage()){
                        $price = 0;
                    }
                }
            }

        } catch (DeliveryException $ex) {
            $smarty->assign('isValidMode', false);
        }

        $smarty->assign('chronopostPickupPointDeliveryModePrice', $price);

    }

    /**
     * @param $params
     * @param $smarty
     */
    public function chronopostPickupPointDeliveryType($params, $smarty)
    {
        $smarty->assign('isFresh13Enabled', (bool) ChronopostPickupPoint::getConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_FRESH_DELIVERY_13_STATUS));
        $smarty->assign('isChrono13Enabled', (bool) ChronopostPickupPoint::getConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_13_STATUS));
        $smarty->assign('isChrono18Enabled', (bool) ChronopostPickupPoint::getConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_18_STATUS));
        $smarty->assign('isChrono13BalEnabled', (bool) ChronopostPickupPoint::getConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_13_BAL_STATUS));
        $smarty->assign('isChronoClassicEnabled', (bool) ChronopostPickupPoint::getConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_CLASSIC_STATUS));
        $smarty->assign('isChronoExpressEnabled', (bool) ChronopostPickupPoint::getConfigValue(ChronopostPickupPointConst::CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_EXPRESS_STATUS));
        /** @TODO Add other types of delivery */
    }

}
