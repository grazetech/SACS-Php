<?php

namespace GrazeTech\SabreAPI\Soap;

use GrazeTech\SabreAPI\Configuration\SabreConfig;

class SessionCreateRequest
{
    private $config;

    public function __construct()
    {
        $this->config = SabreConfig::getInstance();
    }

    public function executeRequest()
    {
        $client = new SoapClient("soap/wsdls/SessionCreateRQ/SessionCreateRQ.wsdl", array("uri" => $this->config->getSoapProperty("environment"),
            "location" => $this->config->getSoapProperty("environment"),
            "encoding" => "utf-8",
            "trace" => true,
            'cache_wsdl' => WSDL_CACHE_NONE));
        $responseHeaders = NULL;
        try {
            $client->__soapCall("SessionCreateRQ", $this->createRequestBody(), null, array(SabreSoapClient::createMessageHeader("SessionCreateRQ"),
                $this->createSecurityHeader()), $responseHeaders);
        } catch (SoapFault $e) {
            var_dump($e);
        }
        $result = $responseHeaders["Security"];
        return $result;
    }

    private function createSecurityHeader()
    {
        $securityArray = array(
            "UsernameToken" => array(
                "Username" => $this->config->getSoapProperty("userId"),
                "Password" => $this->config->getSoapProperty("clientSecret"),
                "Domain" => $this->config->getSoapProperty("domain"),
                "Organization" => $this->config->getSoapProperty("group")
            )
        );
        return new SoapHeader("http://schemas.xmlsoap.org/ws/2002/12/secext", "Security", $securityArray, 1);
    }

    private function createRequestBody()
    {
        $result = array("SessionCreateRQ" => array(
                "POS" => array(
                    "Source" => array(
                        "PseudeCityCode" => $this->config->getSoapProperty("group")
                    )
                )
        ));
        return $result;
    }
}
