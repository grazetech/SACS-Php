<?php

use GrazeTech\SabreAPI\Configuration\SabreConfig;
use GrazeTech\SabreAPI\Soap\SabreSoapClient;
use GrazeTech\SabreAPI\Workflow\Activity;

class BargainFinderMaxSoapActivity implements Activity {

    private $config;
    
    public function __construct() {
        $this->config = SabreConfig::getInstance();
    }
    
    public function run(&$sharedContext) {
        $soapClient = new SabreSoapClient("BargainFinderMaxRQ");
        $soapClient->setLastInFlow(false);
        $xmlRequest = $this->getRequest();
        $sharedContext->addResult("BargainFinderMaxRQ", $xmlRequest);
        $sharedContext->addResult("BargainFinderMaxRS", $soapClient->doCall($sharedContext, $xmlRequest));
        return new PassengerDetailsNameOnlyActivity();
    }

    private function getRequest() {
        $request = array("OTA_AirLowFareSearchRQ" => array(
            "_attributes" => array("Version" => $this->config->getSoapProperty("BargainFinderMaxRQVersion")),
            "_namespace" => "http://www.opentravel.org/OTA/2003/05",
            "POS" => array(
                "Source" => array(
                    "_attributes" => array("PseudoCityCode"=>"7TZA"),
                    "RequestorID" => array(
                        "_attributes" => array("ID"=>"1", "Type"=>"1"),
                        "CompanyName" => array(
                            "_attributes" => array("Code"=>"TN")
                        )
                    )
                )
            ),
            "OriginDestinationInformation" => array(
                "DepartureDateTime" => "2016-05-20T10:00:00",
                "OriginLocation" => array("_attributes" => array("LocationCode"=>"JFK")),
                "DestinationLocation" => array("_attributes" => array("LocationCode"=>"LAX")),
                "TPA_Extensions" => array(
                    "SegmentType" => array("_attributes" => array("Code" => "O"))
                )
            ),
            "TravelPreferences" => array(
                "_attributes" => array("ValidInterlineTicket" => "true"),
                "CabinPref" => array("_attributes" => array("Cabin"=>"Y", "PreferLevel"=>"Preferred"))
            ),
            "TravelerInfoSummary" => array(
                "SeatsRequested" => 1,
                "AirTravelerAvail" => array(
                    "PassengerTypeQuantity" => array("_attributes" => array("Code" => "ADT", "Quantity" => "1"))
                )
            ),
            "TPA_Extensions" => array(
                "IntelliSellTransaction" => array(
                    "RequestType" => array("_attributes" => array("Name" => "50ITINS"))
                )
                
            )
        )
        );
        return XMLSerializer::generateValidXmlFromArray($request);
    }

}
