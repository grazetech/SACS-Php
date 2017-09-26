<?php

namespace GrazeTech\SabreAPI\Soap;

use GrazeTech\SabreAPI\Workflow\SharedContext;
use GrazeTech\SabreAPI\Configuration\SabreConfig;
use SoapHeader;
use SoapVar;

class SabreSoapClient
{
    /**
     *
     * @var bool
     */
    private $lastInFlow = false;

    /**
     *
     * @var string
     */
    private $actionName;

    /**
     *
     * @param string $actionName
     */
    public function __construct($actionName)
    {
        $this->actionName = $actionName;
    }

    /**
     *
     * @param SharedContext $sharedContext
     * @param string $request
     * @return string
     */
    public function doCall($sharedContext, $request)
    {
        if ($sharedContext->getResult("SECURITY") == null) {
            error_log("SessionCreate");
            $securityCall = new SessionCreateRequest();
            $sharedContext->addResult("SECURITY", $securityCall->executeRequest());
        }
        $SabreClient = new SabreClient();
        $result = $SabreClient->doCall(SabreSoapClient::getMessageHeaderXml($this->actionName) . $this->createSecurityHeader($sharedContext), $request, $this->actionName);
        if ($this->lastInFlow) {
            error_log("Ignore and close");
            $this->ignoreAndCloseSession($sharedContext->getResult("SECURITY"));
            $sharedContext->addResult("SECURITY", null);
        }
        return $result;
    }

    /**
     *
     * @param type $security
     */
    private function ignoreAndCloseSession($security)
    {
        (new IgnoreTransactionRequest)->executeRequest($security);
        (new SessionCloseRequest)->executeRequest($security);
    }

    /**
     *
     * @param type $sharedContext
     * @return type
     */
    private function createSecurityHeader($sharedContext)
    {
        $security = [
            "Security" => [
                "_namespace" => "http://schemas.xmlsoap.org/ws/2002/12/secext",
                "BinarySecurityToken" => [
                    "_attributes" => ["EncodingType" => "Base64Binary", "valueType" => "String"],
                    "_value" => $sharedContext->getResult("SECURITY")->BinarySecurityToken
                ]
            ]
        ];
        return XMLSerializer::generateValidXmlFromArray($security);
    }

    public static function createMessageHeader($actionString)
    {
        $messageHeaderXml = SabreSoapClient::getMessageHeaderXml($actionString);
        $soapVar = new SoapVar($messageHeaderXml, XSD_ANYXML, null, null, null);
        return new SoapHeader("http://www.ebxml.org/namespaces/messageHeader", "MessageHeader", $soapVar, 1);
    }

    private static function getMessageHeaderXml($actionString)
    {
        $messageHeader = array("MessageHeader" => array(
                "_namespace" => "http://www.ebxml.org/namespaces/messageHeader",
                "From" => array("PartyId" => "sample.url.of.sabre.client.com"),
                "To" => array("PartyId" => "webservices.sabre.com"),
                "CPAId" => "7TZA",
                "ConversationId" => "convId",
                "Service" => $actionString,
                "Action" => $actionString,
                "MessageData" => array(
                    "MessageId" => "1000",
                    "Timestamp" => "2001-02-15T11:15:12Z",
                    "TimeToLive" => "2001-02-15T11:15:12Z"
                )
            )
        );
        return XMLSerializer::generateValidXmlFromArray($messageHeader);
    }

    public function setLastInFlow($lastInFlow)
    {
        $this->lastInFlow = $lastInFlow;
    }
}

class SabreClient
{

    function doCall($headersXml, $body, $action)
    {
        //Data, connection, auth
        $config = SabreConfig::getInstance();
        $soapUrl = $config->getSoapProperty("environment");

        // xml post structure
        $xml_post_string = '<SOAP-ENV:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">'
            . '<SOAP-ENV:Header>'
            . $headersXml
            . '</SOAP-ENV:Header>'
            . '<SOAP-ENV:Body>'
            . $body
            . '</SOAP-ENV:Body>'
            . '</SOAP-ENV:Envelope>';

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: " . $action,
            "Content-length: " . strlen($xml_post_string)
        );

        error_log($action);
        error_log($xml_post_string);
        error_log("------------------------------------------------");

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $soapUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
//            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_VERBOSE, false);

        // converting
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
