<?php

require_once 'vendor/autoload.php'; // Make sure to include the autoload file from the LightSAML library


use LightSaml\Builder\Profile\WebBrowserSso\SpResponseStateBuilder;
use LightSaml\Credential\KeyHelper;
use LightSaml\Credential\X509Credential;
use LightSaml\Meta\Signing\XmlResponseSigner;
use LightSaml\Model\Context\SerializationContext;
use LightSaml\Model\Protocol\Response;
use LightSaml\Model\Assertion\Assertion;
use LightSaml\Model\Assertion\Attribute;
use LightSaml\Model\Assertion\AttributeStatement;
use LightSaml\Model\Assertion\NameID;
use LightSaml\Model\Assertion\NameIDFormat;
use LightSaml\Model\Assertion\Subject;
use LightSaml\Model\Assertion\SubjectConfirmation;
use LightSaml\Model\Assertion\SubjectConfirmationData;

include "inc.php";
include "src/Utility/IdpProvider.php";
include "src/Utility/IdpTools.php";

// Initiating our IdP Provider dummy connection.
$idpProvider = new IdpProvider();

// Instantiating our Utility class.
$idpTools = new IdpTools();

// Receive the HTTP Request and extract the SAMLRequest.
$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$saml_request = $idpTools->readSAMLRequest($request);

// Getting a few details from the message like ID and Issuer.
$issuer = $saml_request->getMessage()->getIssuer()->getValue();
$id = $saml_request->getMessage()->getID();

// Simulate user information from IdP
$user_id = $request->get("username");
$user_email = $idpProvider->getUserEmail();

// Create SP and IdP credentials
$spCredential = new X509Credential($idpProvider->getCertificate(), $idpProvider->getCertificate());
$idpCredential = new X509Credential($idpProvider->getCertificate(), $idpProvider->getCertificate());

// Create a SAML response
$response = new Response();
$response
    ->setID('response-id') // Set a unique ID for the response
    ->setIssueInstant(new \DateTime())
    ->setStatus(new \LightSaml\Model\Protocol\Status())
    ->setDestination('https://accounts.zohoportal.com/accounts/csamlresponse/10072513335') // Replace with your SP ACS (Assertion Consumer Service) URL
    ->setIssuer('https://dev-test.awardregister.com/metadata.php') // Replace with your IdP entity ID
    ->setSignature(new XmlResponseSigner($spCredential)) // Sign the response with SP private key

    // Add an assertion to the response
    ->addAssertion(
        (new Assertion())
            ->setId(\LightSaml\Helper::generateID()) // Set a unique ID for the assertion
            ->setIssueInstant(new \DateTime())
            ->setIssuer('https://dev-test.awardregister.com/metadata.php') // Replace with your IdP entity ID
            ->setSubject(
                (new Subject())
                    ->setNameID(
                        (new NameID())
                            ->setValue($user_id) // Replace with the user's identifier
                            ->setFormat(NameIDFormat::PERSISTENT)
                    )
                    ->addSubjectConfirmation(
                        (new SubjectConfirmation())
                            ->setMethod(SubjectConfirmation::METHOD_BEARER)
                            ->setSubjectConfirmationData(
                                (new SubjectConfirmationData())
                                    ->setNotOnOrAfter(new \DateTime('+1 hour'))
                            )
                    )
            )
            ->setAttributeStatement(
                (new AttributeStatement())
                    ->addAttribute(
                        (new Attribute())
                            ->setName('email')
                            ->setFormat(Attribute::FORMAT_UNSPECIFIED)
                            ->addAttributeValue($user_email) // Replace with the user's email
                    )
                    // Add more attributes as needed
            )
    );

// Serialize the response
$serializationContext = new SerializationContext();
$responseXml = $response->serialize($serializationContext);

// Output the SAML response XML
echo $responseXml;

// Construct a SAML Response.
// $response = $idpTools->createSAMLResponse($idpProvider, $user_id, $user_email, $issuer, $id);

// // Prepare the POST binding (form).
// $bindingFactory = new \LightSaml\Binding\BindingFactory();
// $postBinding = $bindingFactory->create(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST);
// $messageContext = new \LightSaml\Context\Profile\MessageContext();
// $messageContext->setMessage($response);

// // Ensure we include the RelayState.
// $message = $messageContext->getMessage();
// $message->setRelayState($request->get('RelayState'));
// $messageContext->setMessage($message);

// // Return the Response.
// /** @var \Symfony\Component\HttpFoundation\Response $httpResponse */
// $httpResponse = $postBinding->send($messageContext);
// print $httpResponse->getContent();
