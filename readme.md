# Sabre API
Based on [GrazeTech's](https://github.com/grazetech/SabreAPI) namespace conversion.

I've updated it to use environment variables as the primary way to configure the keys.


## Overview
This is a modern PHP PSR-2 compliant fork of the official Sabre distribution. You may load it into your application by adding with composer.

The Sabre API Code Samples for PHP project’s purpose is to provide the reference code and enable quick and easy start to consuming Sabre Webservices. It focuses on business case usage, so it shows how to chain several REST calls into a workflow, where the subsequent call uses the previous one’s result. Its structure is designed to easily reuse parts of the classes, whole classes, modules or even whole project in client’s applications.
## Configuring the application
The configuration is loaded through environment variables. These properties are needed to connect and authenticate to the Sabre’s webservices. They are being read by the *SabreConfig* class, which contains the *getRestProperty()*  or *getSoapProperty()* method.

Your credentials available in the Sabre Dev Studio are given in such a format: `V1:userId:group:domain`, so they should be put into separate properties as environment variables.

* userId - SABRE_USER_ID
* domain - SABRE_DOMAIN
* clientSecret - SABRE_CLIENT_SECRET
* REST environment - SABRE_REST_ENV (defaults to: https://api.test.sabre.com)
* REST formatVersion - SABRE_REST_FORMAT_VERSION (defaults to: V1)
* SOAP environment - SABRE_SOAP_ENV (defaults to: https://sws3-crt.cert.sabre.com)

Although the credentials are not encrypted right now to lower the project entry time, it is strongly recommended to use the encryption in production systems and do not keep the credentials in plain text.

Please register at https://developer.sabre.com in order to obtain your own credentials.
