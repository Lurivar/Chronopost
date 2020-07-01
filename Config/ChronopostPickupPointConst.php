<?php

namespace ChronopostPickupPoint\Config;


use ChronopostPickupPoint\ChronopostPickupPoint;
use Symfony\Component\Filesystem\Filesystem;
use Thelia\Model\ConfigQuery;

class ChronopostPickupPointConst
{
    /** Delivery types Name => Code */
    const CHRONOPOST_PICKUP_POINT_DELIVERY_CODES = [
        "Chrono13"      => "1",
        "Chrono18"      => "16",
        "ChronoExpress" => "17",
        "ChronoClassic" => "44",
        "Chrono13Bal"   => "58",
        "Fresh13"       => "2R",
    ];
    /** @TODO Add other delivery types */

    /** Chronopost shipper identifiers */
    const CHRONOPOST_PICKUP_POINT_CODE_CLIENT                    = "chronopost_pickup_point_code";
    const CHRONOPOST_PICKUP_POINT_PASSWORD                       = "chronopost_pickup_point_password";

    /** Chronopost label type (PDF,ZPL | With or without proof of deposit */
    const CHRONOPOST_PICKUP_POINT_LABEL_TYPE                     = "chronopost_pickup_point_label_type";

    /** Directory where we save the label */
    const CHRONOPOST_PICKUP_POINT_LABEL_DIR                      = "chronopost_pickup_point_label_dir";

    /** ID of the treatment status in Thelia */
    const CHRONOPOST_PICKUP_POINT_TREATMENT_STATUS               = "chronopost_pickup_point_treatment_status";

    /** Send as customer status. */
    const CHRONOPOST_PICKUP_POINT_PRINT_AS_CUSTOMER_STATUS       = "chronopost_pickup_point_send_as_customer_status";

    /** Days before fresh products expiration after processing */
    const CHRONOPOST_PICKUP_POINT_EXPIRATION_DATE                = "chronopost_pickup_point_expiration_date";

    /** Status of the delivery types. Enabled|Disabled */
    const CHRONOPOST_PICKUP_POINT_FRESH_DELIVERY_13_STATUS       = "chronopost_pickup_point_fresh_delivery_13_status";
    const CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_13_STATUS      = "chronopost_pickup_point_delivery_chrono_13_status";
    const CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_18_STATUS      = "chronopost_pickup_point_delivery_chrono_18_status";
    const CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_13_BAL_STATUS  = "chronopost_pickup_point_delivery_chrono_13_bal_status";
    const CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_CLASSIC_STATUS = "chronopost_pickup_point_delivery_chrono_classic_status";
    const CHRONOPOST_PICKUP_POINT_DELIVERY_CHRONO_EXPRESS_STATUS = "chronopost_pickup_point_delivery_chrono_express_status";
    /** @TODO Add other delivery types  */

    /** WSDL for the Chronopost Shipping Service */
    const CHRONOPOST_PICKUP_POINT_SHIPPING_SERVICE_WSDL              = "https://ws.chronopost.fr/shipping-cxf/ShippingServiceWS?wsdl";
    const CHRONOPOST_PICKUP_POINT_RELAY_SEARCH_SERVICE_WSDL          = "https://ws.chronopost.fr/recherchebt-ws-cxf/PointRelaisServiceWS?wsdl";
    const CHRONOPOST_PICKUP_POINT_COORDINATES_SERVICE_WSDL           = "https://ws.chronopost.fr/rdv-cxf/services/CreneauServiceWS?wsdl";
    /** @TODO Add other WSDL config key */

    /** @Unused */
    const CHRONOPOST_PICKUP_POINT_TRACKING_URL                   = "https://ws.chronopost.fr/tracking-cxf/TrackingServiceWS/trackSkybillV2";

    /** Shipper informations */
    const CHRONOPOST_PICKUP_POINT_SHIPPER_NAME1          = "chronopost_pickup_point_shipper_name1";
    const CHRONOPOST_PICKUP_POINT_SHIPPER_NAME2          = "chronopost_pickup_point_shipper_name2";
    const CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS1       = "chronopost_pickup_point_shipper_address1";
    const CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS2       = "chronopost_pickup_point_shipper_address2";
    const CHRONOPOST_PICKUP_POINT_SHIPPER_COUNTRY        = "chronopost_pickup_point_shipper_country";
    const CHRONOPOST_PICKUP_POINT_SHIPPER_CITY           = "chronopost_pickup_point_shipper_city";
    const CHRONOPOST_PICKUP_POINT_SHIPPER_ZIP            = "chronopost_pickup_point_shipper_zipcode";
    const CHRONOPOST_PICKUP_POINT_SHIPPER_CIVILITY       = "chronopost_pickup_point_shipper_civ";
    const CHRONOPOST_PICKUP_POINT_SHIPPER_CONTACT_NAME   = "chronopost_pickup_point_shipper_contact_name";
    const CHRONOPOST_PICKUP_POINT_SHIPPER_PHONE          = "chronopost_pickup_point_shipper_phone";
    const CHRONOPOST_PICKUP_POINT_SHIPPER_MOBILE_PHONE   = "chronopost_pickup_point_shipper_mobile_phone";
    const CHRONOPOST_PICKUP_POINT_SHIPPER_MAIL           = "chronopost_pickup_point_shipper_mail";

    /** @Unused */
    public function getTrackingURL()
    {
        $URL = self::CHRONOPOST_PICKUP_POINT_TRACKING_URL;
        $URL .= "language=" . "fr_FR"; //todo Make locale a variable
        $URL .= "&skybillNumber=" . "XXX"; //todo Use real skybill Number -> getTrackingURL(variable)

        return $URL;
    }

    /** Local static config value, used to limit the number of calls to the DB  */
    protected static $config = null;

    /**
     * Set the local static config value
     */
    public static function setConfig()
    {
        $config = [
            /** Chronopost basic informations */
            self::CHRONOPOST_PICKUP_POINT_CODE_CLIENT                => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_CODE_CLIENT),
            self::CHRONOPOST_PICKUP_POINT_LABEL_DIR                  => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_LABEL_DIR),
            self::CHRONOPOST_PICKUP_POINT_LABEL_TYPE                 => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_LABEL_TYPE),
            self::CHRONOPOST_PICKUP_POINT_PASSWORD                   => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_PASSWORD),
            self::CHRONOPOST_PICKUP_POINT_TREATMENT_STATUS           => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_TREATMENT_STATUS),
            self::CHRONOPOST_PICKUP_POINT_PRINT_AS_CUSTOMER_STATUS   => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_PRINT_AS_CUSTOMER_STATUS),
            self::CHRONOPOST_PICKUP_POINT_EXPIRATION_DATE            => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_EXPIRATION_DATE),

            /** Shipper informations */
            self::CHRONOPOST_PICKUP_POINT_SHIPPER_NAME1              => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_SHIPPER_NAME1),
            self::CHRONOPOST_PICKUP_POINT_SHIPPER_NAME2              => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_SHIPPER_NAME2),
            self::CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS1           => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS1),
            self::CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS2           => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_SHIPPER_ADDRESS2),
            self::CHRONOPOST_PICKUP_POINT_SHIPPER_COUNTRY            => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_SHIPPER_COUNTRY),
            self::CHRONOPOST_PICKUP_POINT_SHIPPER_CITY               => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_SHIPPER_CITY),
            self::CHRONOPOST_PICKUP_POINT_SHIPPER_ZIP                => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_SHIPPER_ZIP),
            self::CHRONOPOST_PICKUP_POINT_SHIPPER_CIVILITY           => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_SHIPPER_CIVILITY),
            self::CHRONOPOST_PICKUP_POINT_SHIPPER_CONTACT_NAME       => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_SHIPPER_CONTACT_NAME),
            self::CHRONOPOST_PICKUP_POINT_SHIPPER_PHONE              => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_SHIPPER_PHONE),
            self::CHRONOPOST_PICKUP_POINT_SHIPPER_MOBILE_PHONE       => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_SHIPPER_MOBILE_PHONE),
            self::CHRONOPOST_PICKUP_POINT_SHIPPER_MAIL               => ChronopostPickupPoint::getConfigValue(self::CHRONOPOST_PICKUP_POINT_SHIPPER_MAIL),

            /** END */
        ];

        /** Delivery types */
        foreach (self::getDeliveryTypesStatusKeys() as $statusKey) {
            $config[$statusKey] = ChronopostPickupPoint::getConfigValue($statusKey);
        }

        /** Add a / to the end of the path for the label directory if it wasn't added manually */
        if (substr($config[self::CHRONOPOST_PICKUP_POINT_LABEL_DIR], -1) !== '/') {
            $config[self::CHRONOPOST_PICKUP_POINT_LABEL_DIR] .= '/';
        }

        /** Check if the label directory exists, create it if it doesn't */
        if (!is_dir($config[self::CHRONOPOST_PICKUP_POINT_LABEL_DIR])) {
            $fs = new Filesystem();

            $fs->mkdir($config[self::CHRONOPOST_PICKUP_POINT_LABEL_DIR]);
        }

        /** Set the local static config value */
        self::$config = $config;
    }

    /**
     * Return the local static config value or the value of a given parameter
     *
     * @param null $parameter
     * @return array|mixed|null
     */
    public static function getConfig($parameter = null)
    {
        /** Check if the local config value is set, and set it if it's not */
        if (null === self::$config) {
            self::setConfig();
        }

        /** Return the value of the config parameter given, or null if it wasn't set */
        if (null !== $parameter) {
            return (isset(self::$config[$parameter])) ? self::$config[$parameter] : null;
        }

        /** Return the local static config value */
        return self::$config;
    }

    public static function getDeliveryTypesStatusKeys()
    {
        $statusKeys = [];

        foreach (self::CHRONOPOST_PICKUP_POINT_DELIVERY_CODES as $name => $code) {
            $statusKeys[$name] = 'chronopost_pickup_point_delivery_' . strtolower($name) . '_status';
        }

        return $statusKeys;
    }
}