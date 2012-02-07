<?php
/*
Module: Dot.tk WHMCS Registrar Module.
Author: Ahmad Mukoshy
Author URL: http://www.mukoshy.com
Version: v0.1
Date: 05/02/2012
*/

function tkshare_getConfigArray() {
	$configarray = array(
	 "apiemail" => array( "Type" => "text", "Size" => "20", "Description" => "DomainShare Email", ),
	 "apipw" => array( "Type" => "password", "Size" => "20", "Description" => "DomainShare Password", ),

	);
	return $configarray;
}
require("domainshare.php");
// Turn on verbose console logging
$ds_debug = true;



function tkshare_RegisterDomain($params) {
	
	// Domain Share Credentials
	$email = $params["apiemail"];
	$pw = $params["apipw"];
	
	//formulate the domain form tld and sld.
	$tld = $params["tld"];
	$sld = $params["sld"];
	$domain = "$sld.$tld";
	


	$regperiod = $params["regperiod"];
	$ns1 = $params["ns1"];
	$ns2 = $params["ns2"];
    $ns3 = $params["ns3"];
    $ns4 = $params["ns4"];
	
	# Registrant Details
	$RegistrantFirstName = $params["firstname"];
	$RegistrantLastName = $params["lastname"];
	$RegistrantAddress1 = $params["address1"];
	$RegistrantAddress2 = $params["address2"];
	$RegistrantCity = $params["city"];
	$RegistrantStateProvince = $params["state"];
	$RegistrantPostalCode = $params["postcode"];
	$RegistrantCountry = $params["country"];
	$RegistrantEmailAddress = $params["email"];
	$RegistrantPhone = $params["phonenumber"];
	# Admin Details
	$AdminFirstName = $params["adminfirstname"];
	$AdminLastName = $params["adminlastname"];
	$AdminAddress1 = $params["adminaddress1"];
	$AdminAddress2 = $params["adminaddress2"];
	$AdminCity = $params["admincity"];
	$AdminStateProvince = $params["adminstate"];
	$AdminPostalCode = $params["adminpostcode"];
	$AdminCountry = $params["admincountry"];
	$AdminEmailAddress = $params["adminemail"];
	$AdminPhone = $params["adminphonenumber"];
	
	# Register Domain with tkshare API Request.
	$reg = domainshare_register($email,$pw,$domain,$RegistrantEmailAddress,12,array("$ns1","$ns2"),null);
	if(empty($reg)){
		$values["error"] = "Registration failed!";
		return $values;
	}else{
		$values["error"] = $reg['reason'];
		return $values;
	}
}

function tkshare_renewDomain($params){
	
	// Domain Share Credentials
	$email = $params["apiemail"];
	$pw = $params["apipw"];
	
	//formulate the domain form tld and sld.
	$tld = $params["tld"];
	$sld = $params["sld"];
	$domain = "$sld.$tld";
	
	# Renewing a Domain Name.
	$renew = domainshare_renew($email,$pw,$domain);
	if(empty($renew)){
		$values["error"] = "Renewal failed!";
		return $values;
	}else{
		$values["error"] = $renew['reason'];
		return $values;
	}
}

function tkshare_GetEPPCode($params) {
	// Domain Share Credentials
	$email = $params["apiemail"];
	$pw = $params["apipw"];
	
	//formulate the domain form tld and sld.
	$tld = $params["tld"];
	$sld = $params["sld"];
	$domain = "$sld.$tld";
	
	
	$send_activation = domainshare_resend_email($email,$pw,$domain);
	$values["eppcode"] = "AUTH CODE will be sent to your email shortly by the DOT.TK Registry";
	return $values;
}

?>