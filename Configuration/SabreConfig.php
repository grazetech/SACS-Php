<?php
namespace GrazeTech\SabreAPI\Configuration;

class SabreConfig
{
    /**
     *
     * @var array
     */
    private $restConfig = [];

    /**
     *
     * @var array
     */
    private $soapConfig = [];

    /**
     *
     * @var SabreConfig
     */
    private static $instance = null;

    /**
     *
     */
    public function __construct()
    {
        $basicConfig = [
            'userId' => getenv('SABRE_USER_ID'),
            'domain' => getenv('SABRE_DOMAIN'),
            'clientSecret' => getenv('SABRE_CLIENT_SECRET'),
        ];

        $this->restConfig = array_merge($basicConfig, [
            'environment' => (getenv('SABRE_REST_ENV')) ?: "https://api.test.sabre.com",
            'formatVersion' => (getenv('SABRE_REST_FORMAT_VERSION')) ?: "V1",
        ]);

        $this->soapConfig = array_merge($basicConfig, [
            "environment" => (getenv('SABRE_SOAP_ENV')) ?: "https://sws3-crt.cert.sabre.com",
            "OTA_PingRQVersion" => "1.0.0",
            "TravelItineraryReadRQVersion" => "3.6.0",
            "PassengerDetailsRQVersion" => "3.2.0",
            "IgnoreTransactionLLSRQVersion" => "2.0.0",
            "BargainFinderMaxRQVersion" => "1.9.2",
            "EnhancedAirBookRQVersion" => "3.2.0",
        ]);
    }

    /**
     *
     * @return SabreConfig
     */
    public static function getInstance()
    {
        return (SabreConfig::$instance === null) ? (SabreConfig::$instance = new SabreConfig) : SabreConfig::$instance;
    }

    /**
     *
     * @param string $propertyName
     * @return string
     */
    public function getRestProperty(string $propertyName)
    {
        return isset($this->restConfig[$propertyName]) ? $this->restConfig[$propertyName] : null;
    }

    /**
     *
     * @param string $propertyName
     * @return string
     */
    public function getSoapProperty(string $propertyName)
    {
        return isset($this->soapConfig[$propertyName]) ? $this->soapConfig[$propertyName] : null;
    }
}
