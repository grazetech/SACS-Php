<?php

use GrazeTech\SabreAPI\Configuration\SabreConfig;
use GrazeTech\SabreAPI\Soap\SabreSoapClient;
use GrazeTech\SabreAPI\Workflow\Activity;

class EnhancedAirBookActivity implements Activity {

    private $config;
    
    public function __construct() {
        $this->config = SabreConfig::getInstance();
    }

    public function run(&$sharedContext) {
        $soapClient = new SabreSoapClient("EnhancedAirBookRQ");
        $soapClient->setLastInFlow(false);

        $bfmResult = $sharedContext->getResult("BargainFinderMaxRS");
        $xmlRequest = $this->getRequest($bfmResult);
        $sharedContext->addResult("EnhancedAirBookRQ", $xmlRequest);
        $sharedContext->addResult("EnhancedAirBookRS", $soapClient->doCall($sharedContext, $xmlRequest));
        return new PassengerDetailsActivity();
    }
    
    private function getRequest($bfmResult) {
        $bfm = new DOMDocument();
        $bfm->loadXML($bfmResult);
        $flightSegment = $bfm->getElementsByTagName("FlightSegment")->item(0);
        $destinationLocation = $flightSegment->getElementsByTagName("ArrivalAirport")->item(0)->getAttributeNode("LocationCode")->value;
        $equipment = $flightSegment->getElementsByTagName("Equipment")->item(0)->getAttributeNode("AirEquipType")->value;
        $marketingAirlineCode = $flightSegment->getElementsByTagName("MarketingAirline")->item(0)->getAttributeNode("Code")->value;
        $marketingAirlineFlightNumber = $flightSegment->getAttributeNode("FlightNumber")->value;
        $operatingAirlineCode = $flightSegment->getElementsByTagName("OperatingAirline")->item(0)->getAttributeNode("Code")->value;
        $originLocation = $flightSegment->getElementsByTagName("DepartureAirport")->item(0)->getAttributeNode("LocationCode")->value;
        $departureDateTime = $flightSegment->getAttributeNode("DepartureDateTime")->value;
        $flightNumber = $flightSegment->getAttributeNode("FlightNumber")->value;
        $numberInParty = "1";
        $resBookDesigCode = $flightSegment->getAttributeNode("ResBookDesigCode")->value;

        $requestArray = array(
            "EnhancedAirBookRQ" => array(
                "_attributes" => array(
                    "HaltOnError" => "false",
                    "version" => $this->config->getSoapProperty("EnhancedAirBookRQVersion")
                ),
                "_namespace" => "http://services.sabre.com/sp/eab/v3_2",
                "OTA_AirBookRQ" => array(
                    "OriginDestinationInformation" => array(
                        "FlightSegment" => array(
                            "_attributes" => array(
                                "DepartureDateTime" => $departureDateTime,
                                "FlightNumber" => $flightNumber,
                                "NumberInParty" => $numberInParty,
                                "ResBookDesigCode" => $resBookDesigCode,
                                "Status" => "NN"
                            ),
                            "DestinationLocation" => array("_attributes" => array("LocationCode" => $destinationLocation)),
                            "Equipment" => array("_attributes" => array("AirEquipType" => $equipment)),
                            "MarketingAirline" => array("_attributes" => array("Code" => $marketingAirlineCode, "FlightNumber" => $marketingAirlineFlightNumber)),
                            "OperatingAirline" => array("_attributes" => array("Code" => $operatingAirlineCode)),
                            "OriginLocation" => array("_attributes" => array("LocationCode" => $originLocation))
                        )
                    )
                ),
                "OTA_AirPriceRQ" => array(
                    "PriceRequestInformation" => array(
                        "_attributes" => array("Retain" => "true"),
                        "OptionalQualifiers" => array(
                            "PricingQualifiers" => array(
                                "PassengerType" => array("_attributes" => array("Code" => "ADT", "Quantity" => "1"))
                            )
                        )
                    )
                ),
                "PostProcessing" => array(
                    "RedisplayReservation" => array("_attributes" => array("WaitInterval" => "2000"))
                )
            )
        );
        return XMLSerializer::generateValidXmlFromArray($requestArray);
    }
}
