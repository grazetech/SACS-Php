<?php

namespace GrazeTech\SabreAPI\Soap;

class SessionCloseRequest
{
    private $config;

    public function __construct()
    {
        $this->config = SabreConfig::getInstance();
    }

    public function executeRequest($security)
    {
        $client = new SoapClient("soap/wsdls/SessionCloseRQ/SessionCloseRQ.wsdl", array("uri" => $this->config->getSoapProperty("environment"),
            "location" => $this->config->getSoapProperty("environment"),
            "encoding" => "utf-8",
            "trace" => true,
            'cache_wsdl' => WSDL_CACHE_NONE));
        try {
            $result = $client->__soapCall("SessionCloseRQ", $this->createRequestBody(), null, array(SabreSoapClient::createMessageHeader("SessionCloseRQ"),
                $this->createSecurityHeader($security)));
        } catch (SoapFault $e) {
            var_dump($e);
        }
        return $result;
    }

    private function createSecurityHeader($security)
    {
        $securityArray = array(
            "BinarySecurityToken" => $security->BinarySecurityToken
        );
        return new SoapHeader("http://schemas.xmlsoap.org/ws/2002/12/secext", "Security", $securityArray, 1);
    }

    private function createRequestBody()
    {
        $result = array("SessionCloseRQ" => array(
                "POS" => array(
                    "Source" => array(
                        "PseudeCityCode" => $this->config->getSoapProperty("group")
                    )
                )
        ));
        return $result;
    }
}
