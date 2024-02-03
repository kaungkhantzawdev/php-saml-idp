<?php

class IdpTools{

  /**
   * Reads a SAMLRequest from the HTTP request and returns a messageContext.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The HTTP request.
   *
   * @return \LightSaml\Context\Profile\MessageContext
   *   The MessageContext that contains the SAML message.
   */
  public function readSAMLRequest($request){

    // We use the Binding Factory to construct a new SAML Binding based on the
    // request.
    $bindingFactory = new \LightSaml\Binding\BindingFactory();
    $binding = $bindingFactory->getBindingByRequest($request);

    // We prepare a message context to receive our SAML Request message.
    $messageContext = new \LightSaml\Context\Profile\MessageContext();

    // The receive method fills in the messageContext with the SAML Request data.
    /** @var \LightSaml\Model\Protocol\Response $response */
    $binding->receive($request, $messageContext);

    return $messageContext;
  }

  /**
   * Constructs a SAML Response.
   *
   * @param \IdpProvider $idpProvider
   * @param $user_id
   * @param $user_email
   * @param $issuer
   * @param $id
   */
  public function createSAMLResponse($idpProvider, $user_id, $user_email, $issuer, $id){


    $acsUrl = $idpProvider->getServiceProviderAcs($issuer);
    // echo "<pre>";
    // // Preparing the response XML
    //   $serializationContext = new \LightSaml\Model\Context\SerializationContext();
    //   echo "serialization <br>";
    //   var_dump($serializationContext);

    //   // We now start constructing the SAML Response using LightSAML.
    //   $response = new \LightSaml\Model\Protocol\Response();
    //   // $response->setRelayState('aHR0cHM6Ly9yaW9tYWMuem9ob2Rlc2suY29tL3BvcnRhbC8=');
    //   $response
    //       ->addAssertion($assertion = new \LightSaml\Model\Assertion\Assertion())
    //       ->setStatus(new \LightSaml\Model\Protocol\Status(
    //           new \LightSaml\Model\Protocol\StatusCode(
    //             \LightSaml\SamlConstants::STATUS_SUCCESS)
    //           )
    //       )
    //       ->setID(\LightSaml\Helper::generateID())
    //       ->setIssueInstant(new \DateTime())
    //       ->setDestination($acsUrl)
    //       // We obtain the Entity ID from the Idp.
    //       ->setIssuer(new \LightSaml\Model\Assertion\Issuer($idpProvider->getIdPId()))
    //   ;

    //   $assertion
    //       ->setId(\LightSaml\Helper::generateID())
    //       ->setIssueInstant(new \DateTime())
    //       // We obtain the Entity ID from the Idp.
    //       ->setIssuer(new \LightSaml\Model\Assertion\Issuer($idpProvider->getIdPId()))
    //       ->setSubject(
    //           (new \LightSaml\Model\Assertion\Subject())
    //               // Here we set the NameID that identifies the name of the user.
    //               ->setNameID(new \LightSaml\Model\Assertion\NameID(
    //                 $user_id,
    //                   \LightSaml\SamlConstants::NAME_ID_FORMAT_UNSPECIFIED
    //               ))
    //               ->addSubjectConfirmation(
    //                   (new \LightSaml\Model\Assertion\SubjectConfirmation())
    //                       ->setMethod(\LightSaml\SamlConstants::CONFIRMATION_METHOD_BEARER)
    //                       ->setSubjectConfirmationData(
    //                           (new \LightSaml\Model\Assertion\SubjectConfirmationData())
    //                               // We set the ResponseTo to be the id of the SAMLRequest.
    //                               ->setInResponseTo($id)
    //                               ->setNotOnOrAfter(new \DateTime('+1 MINUTE'))
    //                               // The recipient is set to the Service Provider ACS.
    //                               ->setRecipient($acsUrl)
    //                       )
    //               )
    //       )
    //       ->setConditions(
    //           (new \LightSaml\Model\Assertion\Conditions())
    //               ->setNotBefore(new \DateTime())
    //               ->setNotOnOrAfter(new \DateTime('+1 MINUTE'))
    //               ->addItem(
    //                   // Use the Service Provider Entity ID as AudienceRestriction.
    //                   new \LightSaml\Model\Assertion\AudienceRestriction([$issuer])
    //               )
    //       )
    //       ->addItem(
    //           (new \LightSaml\Model\Assertion\AttributeStatement())
    //               ->addAttribute(new \LightSaml\Model\Assertion\Attribute(
    //                   \LightSaml\ClaimTypes::EMAIL_ADDRESS,
    //                 // Setting the user email address.
    //                 $user_email
    //               ))
    //       )
    //       ->addItem(
    //           (new \LightSaml\Model\Assertion\AuthnStatement())
    //               ->setAuthnInstant(new \DateTime('-10 MINUTE'))
    //               ->setSessionIndex($assertion->getId())
    //               ->setAuthnContext(
    //                   (new \LightSaml\Model\Assertion\AuthnContext())
    //                       ->setAuthnContextClassRef(\LightSaml\SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT)
    //               )
    //       )
    //   ;

    // // Sign the response.
    // $response->setSignature(new \LightSaml\Model\XmlDSig\SignatureWriter($idpProvider->getCertificate(), $idpProvider->getPrivateKey()));
    // print_r($response);
    // // Serialize to XML.
    // $response->serialize($serializationContext->getDocument(), $serializationContext);

    // // Set the postback url obtained from the trusted SPs as the destination.
    // $response->setDestination($acsUrl);
    // return $response;

		$destination = $acsUrl;
	    $cert = $idpProvider->getCertificate();
	    $key = $idpProvider->getPrivateKey();
	    $audience = $issuer;
	    $relaystate = "aHR0cHM6Ly9yaW9tYWMuem9ob2Rlc2suY29tL3BvcnRhbC8=";
	  
	    $certificate = \LightSaml\Credential\X509Certificate::fromFile($cert);	     
	    $privateKey = \LightSaml\Credential\KeyHelper::createPrivateKey($key, '', true);


	    $response = new \LightSaml\Model\Protocol\Response();
	    $response->setRelayState($relaystate);
	    $response
	       ->addAssertion($assertion = new \LightSaml\Model\Assertion\Assertion())
	        ->setID(\LightSaml\Helper::generateID())
	        ->setInResponseTo($authnRequest->getId())
	        ->setIssueInstant(new \DateTime())
	        ->setDestination($destination)
	        ->setIssuer(new \LightSaml\Model\Assertion\Issuer($issuer))
	        ->setStatus(new \LightSaml\Model\Protocol\Status(new \LightSaml\Model\Protocol\StatusCode('urn:oasis:names:tc:SAML:2.0:status:Success')))
	        ->setSignature(new \LightSaml\Model\XmlDSig\SignatureWriter($certificate, $privateKey));
      var_dump($response);
	   
	    $assertion
	        ->setId(\LightSaml\Helper::generateID())
	        ->setIssueInstant(new \DateTime())
	        ->setIssuer(new \LightSaml\Model\Assertion\Issuer($issuer))
	        ->setSubject(
	                (new \LightSaml\Model\Assertion\Subject())
	                    ->setNameID(new \LightSaml\Model\Assertion\NameID(
	                        $user_id,
	                        \LightSaml\SamlConstants::NAME_ID_FORMAT_EMAIL
	                    ))
	                ->addSubjectConfirmation(
	                        (new \LightSaml\Model\Assertion\SubjectConfirmation())
	                       ->setMethod(\LightSaml\SamlConstants::CONFIRMATION_METHOD_BEARER)
	                       ->setSubjectConfirmationData(
	                                (new \LightSaml\Model\Assertion\SubjectConfirmationData())
	                               ->setInResponseTo($id)
	                               ->setNotOnOrAfter(new \DateTime('+1 MINUTE'))
	                               ->setRecipient($relaystate)
	                            )
	                    )
	            )
	            ->setConditions(
	                (new \LightSaml\Model\Assertion\Conditions())
	                    ->setNotBefore(new \DateTime())
	                    ->setNotOnOrAfter(new \DateTime('+1 MINUTE'))
	                    ->addItem(
	                        new \LightSaml\Model\Assertion\AudienceRestriction($audience)
	                    )
	            )
	        ->addItem(
	                (new \LightSaml\Model\Assertion\AttributeStatement())
	                ->addAttribute(new \LightSaml\Model\Assertion\Attribute(
	                        \LightSaml\ClaimTypes::EMAIL_ADDRESS,
	                        $user_email
	                    ))
	                ->addAttribute(new \LightSaml\Model\Assertion\Attribute(
	                        \LightSaml\ClaimTypes::COMMON_NAME,
	                        $user_id
	                    ))
	            )
	        ->addItem(
	                (new \LightSaml\Model\Assertion\AuthnStatement())
	                ->setAuthnInstant(new \DateTime('-10 MINUTE'))
	                ->setSessionIndex(\LightSaml\Helper::generateID())
	                ->setAuthnContext(
	                        (new \LightSaml\Model\Assertion\AuthnContext())
	                       ->setAuthnContextClassRef(\LightSaml\SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT)
	                    )
	            )
	        ;

          var_dump($response); die();
  }
}
