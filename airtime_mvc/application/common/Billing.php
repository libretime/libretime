<?php

define("AIRTIME_PRO_FREE_TRIAL_PLAN_ID", 34);
define("WHMCS_AIRTIME_GROUP_ID", 15);

class Billing
{
    public static function getAPICredentials()
    {
        return array(
            "username" => $_SERVER["WHMCS_USERNAME"],
            "password" => $_SERVER["WHMCS_PASSWORD"],
            "url" => "https://account.sourcefabric.com/includes/api.php?accesskey=".$_SERVER["WHMCS_ACCESS_KEY"],
        );
    }

    /** Get the Airtime instance ID of the instance the customer is currently viewing. */
    public static function getClientInstanceId()
    {
        //$currentProduct = Billing::getClientCurrentAirtimeProduct();
        //return $currentProduct["id"];
        //XXX: Major hack attack. Since this function gets called often, rather than querying WHMCS
        //     we're just going to extract it from airtime.conf since it's the same as the rabbitmq username.
        $CC_CONFIG = Config::getConfig();
        $instanceId = $CC_CONFIG['rabbitmq']['user'];
        if (!is_numeric($instanceId)) {
            throw new Exception("Invalid instance id in " . __FUNCTION__ . ": " . $instanceId);
        }
        return $instanceId;
    }

    public static function getProducts()
    {
        //Making this static to cache the products during a single HTTP request.
        //This saves us roundtrips to WHMCS if getProducts() is called multiple times.
        static $products = array();
        if (!empty($products))
        {
            return $products;
        }

        $credentials = self::getAPICredentials();

        $postfields = array();
        $postfields["username"] = $credentials["username"];
        $postfields["password"] = md5($credentials["password"]);
        $postfields["action"] = "getproducts";
        $postfields["responsetype"] = "json";
        //gid is the Airtime product group id on whmcs
        $postfields["gid"] = WHMCS_AIRTIME_GROUP_ID;

        $query_string = "";
        foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";

        $result = self::makeRequest($credentials["url"], $query_string);
        //Logging::info($result["products"]["product"]);
        $products = $result["products"]["product"];

        //Blacklist all free plans
        //Hide the promo plans - we will tell the user if they are eligible for a promo plan
        foreach ($products as $k => $p) {
            if ($p["paytype"] === "free" || strpos($p["name"], "Awesome August 2015") !== false)
            {
                unset($products[$k]);
            }
        }

        return $products;
    }

    public static function getProductPricesAndTypes()
    {
        $products = Billing::getProducts();
        $productPrices = array();
        $productTypes = array();

        foreach ($products as $k => $p) {
            $productPrices[$p["name"]] = array(
                "monthly" => $p["pricing"]["USD"]["monthly"],
                "annually" => $p["pricing"]["USD"]["annually"]
            );
            $productTypes[$p["pid"]] = $p["name"] . " ($" . $productPrices[$p['name']]['monthly'] . "/mo)";
        }
        return array($productPrices, $productTypes);
    }

    /** Get the plan (or product in WHMCS lingo) that the customer is currently on.
     *  @return An associative array containing the fields for the product
     *  */
    public static function getClientCurrentAirtimeProduct()
    {
        static $airtimeProduct = null;
        //Ghetto caching to avoid multiple round trips to WHMCS
        if ($airtimeProduct) {
            return $airtimeProduct;
        }
        $credentials = self::getAPICredentials();

        $postfields = array();
        $postfields["username"] = $credentials["username"];
        $postfields["password"] = md5($credentials["password"]);
        $postfields["action"] = "getclientsproducts";
        $postfields["responsetype"] = "json";
        $postfields["clientid"] = Application_Model_Preference::GetClientId();

        $query_string = "";
        foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";

        $result = self::makeRequest($credentials["url"], $query_string);

        //XXX: Debugging / local testing
        if ($_SERVER['SERVER_NAME'] == "localhost") {
            $_SERVER['SERVER_NAME'] = "bananas.airtime.pro";
        }

        //This code must run on airtime.pro for it to work... it's trying to match
        //the server's hostname with the client subdomain. Once it finds a match
        //between the product and the server's hostname/subdomain, then it
        //returns the ID of that product (aka. the service ID of an Airtime instance)
        foreach ($result["products"]["product"] as $product)
        {
            if (strpos($product["groupname"], "Airtime") === FALSE)
            {
                //Ignore non-Airtime products
                continue;
            }
            else
            {
                if ($product["status"] === "Active") {
                    $airtimeProduct = $product;
                    $subdomain = '';

                    foreach ($airtimeProduct['customfields']['customfield'] as $customField) {
                        if ($customField['name'] === SUBDOMAIN_WHMCS_CUSTOM_FIELD_NAME) {
                            $subdomain = $customField['value'];
                            if (($subdomain . ".airtime.pro") === $_SERVER['SERVER_NAME']) {
                                return $airtimeProduct;
                            }
                        }
                    }
                }
            }
        }
        throw new Exception("Unable to match subdomain to a service ID");
    }

    public static function getClientDetails()
    {
        try {
            $credentials = self::getAPICredentials();

            $postfields = array();
            $postfields["username"] = $credentials["username"];
            $postfields["password"] = md5($credentials["password"]);
            $postfields["action"] = "getclientsdetails";
            $postfields["stats"] = true;
            $postfields["clientid"] = Application_Model_Preference::GetClientId();
            $postfields["responsetype"] = "json";

            $query_string = "";
            foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";

            $arr = self::makeRequest($credentials["url"], $query_string);
            return $arr["client"];
        } catch (Exception $e) {
            Logging::info($e->getMessage());
        }
        return array();
    }

    public static function makeRequest($url, $query_string) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); // WHMCS IP whitelist doesn't support IPv6
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5); //Aggressive 5 second timeout
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $jsondata = curl_exec($ch);
            if (curl_error($ch)) {
                //die("Connection Error: ".curl_errno($ch).' - '.curl_error($ch));
                throw new Exception("WHMCS server down or invalid request.");
            }
            curl_close($ch);

            return json_decode($jsondata, true);
        } catch (Exception $e) {
            Logging::info($e->getMessage());
        }
        return array();
    }

    public static function ensureClientIdIsValid()
    {
        if (Application_Model_Preference::GetClientId() == null)
        {
            throw new Exception("Invalid client ID: " . Application_Model_Preference::GetClientId());
        }
    }


    /**
     * @return True if VAT should be applied to the order, false otherwise.
     */
    public static function checkIfVatShouldBeApplied($vatNumber, $countryCode)
    {
        if ($countryCode === 'UK') {
            $countryCode = 'GB'; //VIES database has it as GB
        }
        //We don't charge you VAT if you're not in the EU
        if (!Billing::isCountryInEU($countryCode))
        {
            return false;
        }

        //So by here, we know you're in the EU.

        //No VAT number? Then we charge you VAT.
        if (empty($vatNumber)) {
            return true;
        }
        //Check if VAT number is valid
        return Billing::validateVATNumber($vatNumber, $countryCode);
    }

    public static function isCountryInEU($countryCode)
    {
        $euCountryCodes = array('BE', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE', 'EL', 'ES', 'FR',
            'HR', 'IT', 'CY', 'LV', 'LT', 'LU', 'HU', 'MT', 'NL', 'AT',
            'PL', 'PT', 'RO', 'SI', 'SK', 'FI', 'SE', 'UK', 'GB');

        if (!in_array($countryCode, $euCountryCodes)) {
            return false;
        }
        return true;
    }

    /**
     * Check if an EU VAT number is valid, using the EU VIES validation web API.
     *
     * @param string $vatNumber - A VAT identifier (number), with or without the two letter country code at the
     *                            start (either one works) .
     * @param string $countryCode - A two letter country code
     * @return boolean true if the VAT number is valid, false otherwise.
     */
    public static function validateVATNumber($vatNumber, $countryCode)
    {
        $vatNumber = str_replace(array(' ', '.', '-', ',', ', '), '', trim($vatNumber));

        //If the first two letters are a country code, use that as the country code and remove those letters.
        $firstTwoCharacters = substr($vatNumber, 0, 2);
        if (preg_match("/[a-zA-Z][a-zA-Z]/", $firstTwoCharacters) === 1) {
            $countryCode = strtoupper($firstTwoCharacters); //The country code from the VAT number overrides your country.
            $vatNumber = substr($vatNumber, 2);
        }
        $client = new SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");

        if($client){
            $params = array('countryCode' => $countryCode, 'vatNumber' => $vatNumber);
            try{
                $r = $client->checkVat($params);
                if($r->valid == true){
                    // VAT-ID is valid
                    return true;
                } else {
                    // VAT-ID is NOT valid
                    return false;
                }
            } catch(SoapFault $e) {
                Logging::error('VIES EU VAT validation error: '.$e->faultstring);
                if ($e->faultstring == "INVALID_INPUT") {
                    return false;
                }
                //If there was another error with the VAT validation service, we allow
                //the VAT number to pass. (eg. SERVER_BUSY, MS_UNAVAILABLE, TIMEOUT, SERVICE_UNAVAILABLE)
                return true;
            }
        } else {
            // Connection to host not possible, europe.eu down?
            Logging::error('VIES EU VAT validation error: Host unreachable');
            //If there was an error with the VAT validation service, we allow
            //the VAT number to pass.
            return true;
        }
        return false;
    }


    public static function addVatToInvoice($invoice_id)
    {
        $credentials = self::getAPICredentials();

        //First we need to get the invoice details: sub total, and total
        //so we can calcuate the amount of VAT to add
        $invoicefields = array();
        $invoicefields["username"] = $credentials["username"];
        $invoicefields["password"] = md5($credentials["password"]);
        $invoicefields["action"] = "getinvoice";
        $invoicefields["invoiceid"] = $invoice_id;
        $invoicefields["responsetype"] = "json";

        $invoice_query_string = "";
        foreach ($invoicefields as $k=>$v) $invoice_query_string .= "$k=".urlencode($v)."&";

        //TODO: error checking
        $result = Billing::makeRequest($credentials["url"], $invoice_query_string);

        $vat_amount = $result["subtotal"] * (VAT_RATE/100);
        $invoice_total = $result["total"] + $vat_amount;

        //Second, update the invoice with the VAT amount and updated total
        $postfields = array();
        $postfields["username"] = $credentials["username"];
        $postfields["password"] = md5($credentials["password"]);
        $postfields["action"] = "updateinvoice";
        $postfields["invoiceid"] = $invoice_id;
        $postfields["tax"] = "$vat_amount";
        $postfields["taxrate"] = strval(VAT_RATE);
        $postfields["total"] = "$invoice_total";
        $postfields["responsetype"] = "json";

        $query_string = "";
        foreach ($postfields as $k=>$v) $query_string .= "$k=".urlencode($v)."&";

        //TODO: error checking
        $result = Billing::makeRequest($credentials["url"], $query_string);
    }

}
