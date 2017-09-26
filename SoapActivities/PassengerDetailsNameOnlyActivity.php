<?php

use GrazeTech\SabreAPI\Configuration\SabreConfig;
use GrazeTech\SabreAPI\Soap\SabreSoapClient;
use GrazeTech\SabreAPI\Workflow\Activity;

class PassengerDetailsNameOnlyActivity implements Activity {

    private $config;
    
    public function __construct() {
        $this->config = SabreConfig::getInstance();
    }

    public function run(&$sharedContext) {
        $soapClient = new SabreSoapClient("PassengerDetailsRQ");
        $soapClient->setLastInFlow(false);
        $xmlRequest = $this->getRequest();
        $sharedContext->addResult("PassengerDetailsNameOnlyRQ", $xmlRequest);
        $sharedContext->addResult("PassengerDetailsNameOnlyRS", $soapClient->doCall($sharedContext, $xmlRequest));
        return new EnhancedAirBookActivity();
    }

    private function getRequest() {
        $requestArray = array(
            "PassengerDetailsRQ" => array(
                "_attributes" => array("HaltOnError" => "true", "IgnoreOnError" => "false", "version" => $this->config->getSoapProperty("PassengerDetailsRQVersion")),
                "_namespace" => "http://services.sabre.com/sp/pd/v3_2",
                "TravelItineraryAddInfoRQ" => array(
                    "CustomerInfo" => array(
                        "ContactNumbers" => array(
                            array(
                                "_attributes" => array("LocationCode" => "DFW", "NameNumber" => "1.1", "Phone" => "817-555-1212", "PhoneUseType" => "H")
                            ),
                            array(
                                "_attributes" => array("LocationCode" => "DFW", "NameNumber" => "1.1", "Phone" => "682-555-1212", "PhoneUseType" => "O")
                            )
                        ),
                        "Email" => array("_attributes" => array("Address" => "webservices.support@sabre.com", "NameNumber" => "1.1")),
                        "PersonName" => array(
                            "_attributes" => array("NameNumber" => "1.1"),
                            "GivenName" => "Sabre".$this->generatePseudoRandomString(5),
                            "Surname" => "TEST".$this->generatePseudoRandomString(5)
                        )
                    )
                )
                )
        );
        
        return XMLSerializer::generateValidXmlFromArray($requestArray, null, "ContactNumber");
    }

    private function generatePseudoRandomString($length) {
        $characters = 'abcdefghijklmnopqrstuvwxyz';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $string;
    }

}
