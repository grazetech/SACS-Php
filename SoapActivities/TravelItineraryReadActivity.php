<?php

use GrazeTech\SabreAPI\Configuration\SabreConfig;
use GrazeTech\SabreAPI\Soap\SabreSoapClient;
use GrazeTech\SabreAPI\Soap\XMLSerializer;
use GrazeTech\SabreAPI\Workflow\Activity;
use GrazeTech\SabreAPI\Workflow\SharedContext;

class TravelItineraryReadActivity implements Activity {

    private $config;
    
    public function __construct() {
        $this->config = SabreConfig::getInstance();
    }

    public function run(SharedContext &$sharedContext) {
        $soapClient = new SabreSoapClient("TravelItineraryReadRQ");
        $soapClient->setLastInFlow(true);
        $doc = new DOMDocument();
        $doc->loadXML($sharedContext->getResult("PassengerDetailsRS"));
        $pnr = $doc->getElementsByTagName("ItineraryRef")->item(0)->getAttributeNode("ID")->value;
        $xmlRequest = $this->getRequest($pnr);
        $sharedContext->addResult("TravelItineraryReadRQ", $xmlRequest);
        $sharedContext->addResult("TravelItineraryReadRS", $soapClient->doCall($sharedContext, $xmlRequest));
        return null;
                
    }

    private function getRequest($pnr) {
        $requestArray = array(
            "TravelItineraryReadRQ" => array(
                "_namespace" => "http://services.sabre.com/res/tir/v3_6",
                "_attributes" => array(
                    "Version" => $this->config->getSoapProperty("TravelItineraryReadRQVersion")
                ),
                "MessagingDetails" => array(
                    "SubjectAreas" => array(
                        "SubjectArea" => "PNR"
                    )
                ),
                "UniqueID" => array(
                    "_attributes" => array("ID" => $pnr)
                )
            )
        );
        return XMLSerializer::generateValidXmlFromArray($requestArray);
    }
}
