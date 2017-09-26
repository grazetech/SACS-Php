<?php

use GrazeTech\SabreAPI\Configuration\SabreConfig;
use GrazeTech\SabreAPI\Soap\SabreSoapClient;
use GrazeTech\SabreAPI\Workflow\Activity;

class PassengerDetailsActivity implements Activity {
    
    private $config;
    
    public function __construct() {
        $this->config = SabreConfig::getInstance();
    }

    public function run(&$sharedContext) {
        $soapClient = new SabreSoapClient("PassengerDetailsRQ");
        $soapClient->setLastInFlow(false);
        $xmlRequest = $this->getRequest();
        $sharedContext->addResult("PassengerDetailsRQ", $xmlRequest);
        $sharedContext->addResult("PassengerDetailsRS", $soapClient->doCall($sharedContext, $xmlRequest));
        return new TravelItineraryReadActivity();
    }
    
    private function getRequest() {
        $requestArray = array(
            "PassengerDetailsRQ" => array(
                "_namespace" => "http://services.sabre.com/sp/pd/v3_2",
                "_attributes" => array(
                    "HaltOnError" => "true",
                    "IgnoreOnError" => "false",
                    "version" => $this->config->getSoapProperty("PassengerDetailsRQVersion")
                ),
                "PostProcessing" => array(
                    "_attributes" => array("RedisplayReservation" => "true"),
                    "EndTransactionRQ" => array(
                        "EndTransaction" => array(
                            "_attributes" => array("Ind" => "true")
                        ),
                        "Source" => array(
                            "_attributes" => array("ReceivedFrom" => "SabreTesting")
                        )
                    )
                ),
                "SpecialReqDetails" => array(
                    "AddRemarkRQ" => array(
                        "RemarkInfo" => array(
                            "FOP_Remark" => array(
                                "_attributes" => array("Type" => "CASH"),
                            ),
                            "1" => array( //<Remark>
                                "_attributes" => array("Type" => "General"),
                                "Text" => "TEST GENERAL REMARK"
                            ),
                            "2" => array( //<Remark>
                                "_attributes" => array("Type" => "Hidden"),
                                "Text" => "TEST HIDDEN REMARK"
                            ),
                            "3" => array( //<Remark>
                                "_attributes" => array("Type" => "Historical"),
                                "Text" => "TEST HISTORICAL REMARK"
                            )
                        )
                    )
                ),
                "TravelItineraryAddInfoRQ" => array(
                    "AgencyInfo" => array(
                        "Ticketing" => array(
                            "_attributes" => array("TicketType" => "7T-A")
                        )
                    )
                )
            )
        );
        return XMLSerializer::generateValidXmlFromArray($requestArray, null, "Remark");
    }
}
