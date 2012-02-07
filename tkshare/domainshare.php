<?php
/*
PHP module for communicating with the DomainShare REST API.

Please see http://www.dot.tk/en/pageH12.html for detailed argument
description and examples.
*/


$ds_debug = false;

function rest_call($call, $args = array())
{
	$base_url = "https://api.domainshare.tk/";
	global $ds_debug;

    if($ds_debug){
		printf("\n******************\n");
	    printf("calling '%s'..\n", $call);
		printf("******************\n");
	}

	$nameservers = null;
	$keywords = null;

	if (array_key_exists("nameservers", $args)) {
		if($args["nameservers"] != null) {
		$nameservers = $args["nameservers"];
		unset($args["nameservers"]);
		}
	}

	if (array_key_exists("keywords", $args)) {
		if($args["keywords"] != null) {
		$keywords = $args["keywords"];
		unset($args["keywords"]);
		}
	}


	foreach ($args as $i => $value) {
		if($value == null)
	    unset($args[$i]);
	}

	if($ds_debug){
		printf("Args: \n");
		var_dump($args);
	}

	$postdata = http_build_query($args);

	if ($nameservers)
		$postdata = $postdata."&nameserver=".join("&nameserver=",  $nameservers);

	if ($keywords)
		$postdata = $postdata."&keyword=".join("&keyword=",  $keywords);


	if($ds_debug){
		printf("postdata: ");
		var_dump($postdata);
	}

	$opts=array("http" => array("method" => "POST",
	"header" => "Content-type: application/x-www-form-urlencoded",
	"content" => $postdata));


	$res = stream_context_create($opts);
	$output = file_get_contents($base_url . $call . ".json", false, $res);

	return json_decode($output,true);
}


/*
    This function is used to check if you are able to the reach the
    DomainShare API.
*/
function domainshare_ping()
{
	$result = rest_call("ping");

	if($result["status"] == "OK")
		return $result["partner_ping"];

	else return $result;
}


/*
    This function is used to check if a free domain name is still available
    with the Dot TK Registry. This function can also be used to obtain
    status information of a domain name.


    Required arguments: email,password,domainname
*/
function domainshare_availability_check($email,$password,$domainname)
{
	$result = rest_call("availability_check",array("email" => $email, "password" => $password, "domainname" => $domainname));

	if($result["status"] == "OK")
		return $result["partner_availability_check"];

	else return $result;
}


/*
    This function is used to register a free domain name with the Dot TK
    Registry. Only available domain names can be registered. A domain name
    can be registered from 1 to 12 months. If no registration period is
    provided, a default registration period of 3 months is used.


    Required arguments: email,password,domainname,enduseremail
                        + nameservers OR forwardurl
*/
function domainshare_register($email,$password,$domainname,$enduseremail,$monthsofregistration=null,$nameservers=null,$forwardurl=null)
{
	$result = rest_call("register",array("email" => $email,"password" => $password, "domainname" => $domainname, "enduseremail" => $enduseremail, "monthsofregistration" => $monthsofregistration, "nameservers" => $nameservers, "forwardurl" => $forwardurl));

	if($result["status"] == "OK")
		return $result["partner_registration"];

	else return $result;
}

/*
    DomainShare domains need to be actively renewed by the DomainShare
    Partner. Renewals may only take place in the last 15 days of any
    registration period.

    Required arguments: email,password,domainname
*/
function domainshare_renew($email,$password,$domainname,$monthsofregistration=null)
{
	$result = rest_call("renew",array("email" => $email,"password" => $password, "domainname" => $domainname, "monthsofregistration" => $monthsofregistration));

	if($result["status"] == "OK")
		return $result["partner_renew"];

	else return $result;
}

/*
    Glue records are needed for domains that are configured to use DNS
    and where the name servers are in the same DNS zone. Example: if you
    register TEST0112.TK and if you want to use name servers within that
    same DNS zone, like NS1.TEST0112.TK and NS2.TEST0112.TK, then you
    need to add NS1.TEST0112.TK and NS2.TEST0112.TK as glue records to
    prevent circular dependency.

    This function adds or modifies glue records. If the glue record
    relates to a domain name that is in the DomainShare partner's portfolio
    and if it has not been registered before, it will be added to the
    database of the Dot TK Registry. If it has been registered before
    by the DomainShare Partner the record will be modified with the new
    IP address.

    Required arguments: email,password,hostname,ipaddress
*/
function domainshare_host_registration($email,$password,$hostname,$ipaddress)
{
	$result = rest_call("host_registration",array("email" => $email,"password" => $password, "hostname" => $hostname, "ipaddress" => $ipaddress));

	if($result["status"] == "OK")
		return $result["partner_host_registration"];

	else return $result;
}

/*
    Glue records are needed for domains that are configured to use DNS
    and where the name servers are in the same DNS zone. With this
    function you can remove existing glue records.

    Required arguments: email,password,hostname
*/
function domainshare_host_removal($email,$password,$hostname)
{
	$result = rest_call("host_removal",array("email" => $email,"password" => $password, "hostname" => $hostname));

	if($result["status"] == "OK")
		return $result["partner_host_removal"];

	else return $result;
}

/*
    Glue records are needed for domains that are configured to use DNS and
    where the name servers are in the same DNS zone. With this function you can
    list existing glue records of a domain name.

    Required arguments: email,password,domainname
*/
function domainshare_host_list($email,$password,$domainname)
{
	$result = rest_call("host_list",array("email" => $email,"password" => $password, "domainname" => $domainname));

	if($result["status"] == "OK")
		return $result["partner_host_list"];

	else return $result;
}


/*
    This function is used to modify the settings of a domain name. It's
    possible to change the name servers or the forwarding URL. Please
    note that it can take up to 30 minutes before every DNS in the
    world is updated with the new information. Any modification of
    a domain will remove any old settings for this domain.

    Required arguments: email,password,domainname
                        + nameservers OR forwardurl
*/
function domainshare_modify($email,$password,$domainname,$nameservers=null,$forwardurl=null)
{
	$result = rest_call("modify",array("email" => $email,"password" => $password, "domainname" => $domainname, "nameservers" => $nameservers, "forwardurl" => $forwardurl));

	if($result["status"] == "OK")
		return $result["partner_modify"];

	else return $result;
}

/*
    DomainShare Partners are registering domain names for others (endusers).
    Endusers need to confirm their email address with Dot TK before a
    domain is fully authorized. During initial domain name registration
    this confirmation email is sent to the enduser. This function is
    resending the confirmation email. It can be used in case the enduser
    has not received the Dot TK confirmation email. When this function is
    used for domains that are already confirmed, an email with the enduser
    Dot TK Registration ID is sent.

    Required arguments: email,password,domainname
*/
function domainshare_resend_email($email,$password,$domainname,$enduseremail=null)
{
	$result = rest_call("resend_email",array("email" => $email,"password" => $password, "domainname" => $domainname, "enduseremail" => $enduseremail));

	if($result["status"] == "OK")
		return $result["partner_resend_email"];

	else return $result;
}

/*
    DomainShare Partners can opt to deactivate domain names at any time.
    Deactivation of a domain name can only take place if a valid
    reason is provided.

    Valid reasons are:
    adult, adult_gay, drugs, violence, gambling, only_ads, does_not_exist,
    virus_spyware, weapons

    Please see http://www.dot.tk/en/pageH12.html for further info.

    Required arguments: email,password,domainname,reason
*/
function domainshare_domain_deactivate($email,$password,$domainname,$reason)
{
	$result = rest_call("domain_deactivate",array("email" => $email,"password" => $password, "domainname" => $domainname, "reason" => $reason));

	if($result["status"] == "OK")
		return $result["partner_domain_deactivate"];

	else return $result;
}


/*
    DomainShare Partners can opt to reactivate a previously deactivated
    domain. There are two requirements to reactivate domains. Both
    requirements should be met to successfully reactivate a domain name.
    The first requirement is that reactivation can only be done if the
    domain was deactivated because it had a reason value of does_not_exist.
    The second requirement is that only domain names that have been
    deactivated less than 15 days ago can be reactivated.

    The reactivation function is mostly used if the DomainShare Partner
    made a honest mistake in deactivating a domain name.

    Required arguments: email,password,domainname
*/
function domainshare_domain_reactivate($email,$password,$domainname)
{
	$result = rest_call("domain_reactivate",array("email" => $email,"password" => $password, "domainname" => $domainname));

	if($result["status"] == "OK")
		return $result["partner_domain_reactivate"];

	else return $result;
}

/*
    This function allows DomainShare Partners to increase their revenue on
    domain names that show a parking page with advertisements. The
    DomainShare Partner is able to set a category of the domain and one
    or more keywords. The Dot TK Registry and its advertising partners
    do take the suggestions of the DomainShare Partner seriously when
    deciding on what subjects advertisements should be displayed.
    However, it is in Dot TK's sole discretion to display any
    advertisements based on different categories or keywords.

    Required arguments: email,password,domainname
                        + category OR keywords OR both
*/
function domainshare_update_parking($email,$password,$domainname,$category=null,$keywords=null)
{
	$result = rest_call("update_parking",array("email" => $email,"password" => $password, "domainname" => $domainname, "category" => $category, "keywords" => $keywords));

	if($result["status"] == "OK")
		return $result["partner_update_parking"];

	else return $result;
}

?>
