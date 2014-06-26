<?php

define('VAT_RATE', 19.00);

class BillingController extends Zend_Controller_Action {

    public function init()
    {
        //Two of the actions in this controller return JSON because they're used for AJAX:
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('vat-validator', 'json')
                    ->addActionContext('is-country-in-eu', 'json')
                    ->initContext();
    }
    
    public function indexAction()
    {
        
    }

    public function upgradeAction()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headLink()->appendStylesheet($baseUrl.'css/billing.css?'.$CC_CONFIG['airtime_version']);
        BillingController::ensureClientIdIsValid();
        
        $request = $this->getRequest();
        $form = new Application_Form_BillingUpgradeDowngrade();
        if ($request->isPost()) {
            
            /*
             * TODO: determine if VAT shoould be charged on the invoice or not.
             * We'll need to check if a VAT number was supplied in the form and if so,
             * validate it somehow. We'll also need to make sure the country given is
             * in the EU
             */
                        
            $formData = $request->getPost();
            if ($form->isValid($formData)) {
                $credentials = self::getAPICredentials();
                
                $apply_vat = BillingController::checkIfVatShouldBeApplied($formData["customfields"]["7"], $formData["country"]);
                
                $postfields = array();
                $postfields["username"] = $credentials["username"];
                $postfields["password"] = md5($credentials["password"]);
                $postfields["action"] = "upgradeproduct";
                $postfields["clientid"] = Application_Model_Preference::GetClientId();
                
                $postfields["serviceid"] = self::getClientInstanceId();
                $postfields["type"] = "product";
                $postfields["newproductid"] = $formData["newproductid"];
                $postfields["newproductbillingcycle"] = $formData["newproductbillingcycle"];
                $postfields["paymentmethod"] = $formData["paymentmethod"];
                $postfields["responsetype"] = "json";
                
                $upgrade_query_string = "";
                foreach ($postfields AS $k=>$v) $upgrade_query_string .= "$k=".urlencode($v)."&";
                
                //update client info

                $clientfields = array();
                $clientfields["username"] = $credentials["username"];
                $clientfields["password"] = md5($credentials["password"]);
                $clientfields["action"] = "updateclient";                
                $clientfields["clientid"] = Application_Model_Preference::GetClientId();
                $clientfields["customfields"] = base64_encode(serialize($formData["customfields"]));
                unset($formData["customfields"]);
                $clientfields["responsetype"] = "json";
                unset($formData["newproductid"]);
                unset($formData["newproductbillingcycle"]);
                unset($formData["paymentmethod"]);
                unset($formData["action"]);
                $clientfields = array_merge($clientfields, $formData);
                unset($clientfields["password2verify"]);
                unset($clientfields["submit"]);
                $client_query_string = "";
                foreach ($clientfields AS $k=>$v) $client_query_string .= "$k=".urlencode($v)."&";
                
                $result = $this->makeRequest($credentials["url"], $client_query_string);
                Logging::info($result);
                if ($result["result"] == "error") {
                    $this->setErrorMessage();
                    $this->view->form = $form;
                } else {
                    $result = $this->makeRequest($credentials["url"], $upgrade_query_string);
                    if ($result["result"] == "error") {
                        Logging::info($_SERVER['HTTP_HOST']." - Account upgrade failed. - ".$result["message"]);
                        $this->setErrorMessage();
                        $this->view->form = $form;
                    } else {
                        if ($apply_vat) {
                            $this->addVatToInvoice($result["invoiceid"]);
                        }
                        self::viewInvoice($result["invoiceid"]);
                    }
                }
            } else {
                $this->view->form = $form;
            }
        } else {
            $this->view->form = $form;
        }
    }
    
    
    public function isCountryInEuAction()
    {
        // Disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
 
        $request = $this->getRequest();
        if (!$request->isPost()) {
            throw new Exception("Must POST data to isCountryInEuAction.");
        }
        $formData = $request->getPost();
    
        //Set the return JSON value
        $this->_helper->json(array("result"=>BillingController::isCountryInEU($formData["country"])));
    }
    
    public function vatValidatorAction()
    {
        // Disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $request = $this->getRequest();
        if (!$request->isPost()) {
            throw new Exception("Must POST data to vatValidatorAction.");
        }
        $formData = $request->getPost();
        
        $vatNumber = trim($formData["vatnumber"]);
        if (empty($vatNumber)) {
            $this->_helper->json(array("result"=>false));
        }
        
        //Set the return JSON value
        $this->_helper->json(array("result"=>BillingController::checkIfVatShouldBeApplied($vatNumber, $formData["country"])));
    }
    
    /**
     * @return True if VAT should be applied to the order, false otherwise.
     */
    private static function checkIfVatShouldBeApplied($vatNumber, $countryCode)
    {
        if ($countryCode === 'UK') {
            $countryCode = 'GB'; //VIES database has it as GB
        }
        //We don't charge you VAT if you're not in the EU
        if (!BillingController::isCountryInEU($countryCode))
        {
            return false;
        }
        
        //So by here, we know you're in the EU.
        
        //No VAT number? Then we charge you VAT.
        if (empty($vatNumber)) {
            return true;
        }
        //Check if VAT number is valid
        return BillingController::validateVATNumber($vatNumber, $countryCode);
    }
    
    private static function isCountryInEU($countryCode)
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
    private static function validateVATNumber($vatNumber, $countryCode)
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
                return false;
            }
        } else {
            // Connection to host not possible, europe.eu down?
            Logging::error('VIES EU VAT validation error: Host unreachable');
            return false;
        }
        return false;
    }
    

    private function addVatToInvoice($invoice_id)
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
        $result = $this->makeRequest($credentials["url"], $invoice_query_string);
        
        $vat_amount = $result["subtotal"] * (VAT_RATE/100);
        $invoice_total = $result["total"] + $vat_amount;

        //Second, update the invoice with the VAT amount and updated total
        $postfields = array();
        $postfields["username"] = $credentials["username"];
        $postfields["password"] = md5($credentials["password"]);
        $postfields["action"] = "updateinvoice";
        $postfields["invoiceid"] = $invoice_id;
        $postfields["tax"] = "$vat_amount";
        $postfields["taxrate"] = "$vat_rate";
        $postfields["total"] = "$invoice_total";
        $postfields["responsetype"] = "json";
        
        $query_string = "";
        foreach ($postfields as $k=>$v) $query_string .= "$k=".urlencode($v)."&";
        
        //TODO: error checking
        $result = $this->makeRequest($credentials["url"], $query_string);
    }

    private function setErrorMessage($msg=null)
    {
        if (!is_null($msg)) {
            $this->view->errorMessage = $msg;
        } else {
            $this->view->errorMessage = "An error occurred and we could not update your account. Please contact support for help.";
        }
    }

    private function setSuccessMessage($msg=null)
    {
        if (!is_null($msg)) {
            $this->view->successMessage = $msg;
        } else {
            $this->view->successMessage = "Your account has been updated.";
        }
    }

    private static function getAPICredentials()
    {
        return array(
            "username" => $_SERVER["WHMCS_USERNAME"],
            "password" => $_SERVER["WHMCS_PASSWORD"],
            "url" => "https://account.sourcefabric.com/includes/api.php?accesskey=".$_SERVER["WHMCS_ACCESS_KEY"],
        );
    }

    private static function viewInvoice($invoice_id)
    {
        $whmcsurl = "https://account.sourcefabric.com/dologin.php";
        $autoauthkey = $_SERVER["WHMCS_AUTOAUTH_KEY"];
        $timestamp = time(); //whmcs timezone?
        $client = self::getClientDetails();
        $email = $client["email"];
        $hash = sha1($email.$timestamp.$autoauthkey);
        $goto = "viewinvoice.php?id=".$invoice_id;
        header("Location: ".$whmcsurl."?email=$email&timestamp=$timestamp&hash=$hash&goto=$goto");
    }

    public function clientAction()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headLink()->appendStylesheet($baseUrl.'css/billing.css?'.$CC_CONFIG['airtime_version']);
        
        $request = $this->getRequest();
        $form = new Application_Form_BillingClient();
        BillingController::ensureClientIdIsValid();
        if ($request->isPost()) {
            $formData = $request->getPost();
            if ($form->isValid($formData)) {
            
                $credentials = self::getAPICredentials();
                
                $postfields = array();
                $postfields["username"] = $credentials["username"];
                $postfields["password"] = md5($credentials["password"]);
                $postfields["action"] = "updateclient";

                $postfields["customfields"] = base64_encode(serialize($formData["customfields"]));
                unset($formData["customfields"]);
                
                $postfields["clientid"] = Application_Model_Preference::GetClientId();
                $postfields["responsetype"] = "json";
                $postfields = array_merge($postfields, $formData);
                unset($postfields["password2verify"]);
                unset($postfields["submit"]);
                
                $query_string = "";
                foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";
                
                $result = $this->makeRequest($credentials["url"], $query_string);

                if ($result["result"] == "error") {
                    $this->setErrorMessage();
                } else {
                    $form = new Application_Form_BillingClient();
                    $this->setSuccessMessage();
                }
                
                $this->view->form = $form;
            } else {
                $this->view->form = $form;
            }
        } else {
            $this->view->form = $form;
        }
    }

    public function invoicesAction()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headLink()->appendStylesheet($baseUrl.'css/billing.css?'.$CC_CONFIG['airtime_version']);
        
        BillingController::ensureClientIdIsValid();
        $credentials = self::getAPICredentials();
        
        $postfields = array();
        $postfields["username"] = $credentials["username"];
        $postfields["password"] = md5($credentials["password"]);
        $postfields["action"] = "getinvoices";
        $postfields["responsetype"] = "json";
        $postfields["clientid"] = Application_Model_Preference::GetClientId();
        
        $query_string = "";
        foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";
        
        $result = self::makeRequest($credentials["url"], $query_string);
        
        $this->view->invoices = $result["invoices"]["invoice"];
    }
    
    public function invoiceAction()
    {
        BillingController::ensureClientIdIsValid();
        $request = $this->getRequest();
        $invoice_id = $request->getParam('invoiceid');
        self::viewInvoice($invoice_id);
    }

    private static function getClientInstanceId()
    {
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
        Logging::info($result);
        
        //XXX: Debugging / local testing
        if ($_SERVER['SERVER_NAME'] == "airtime.localhost") {
            return "1384";
        }
        
        //This code must run on airtime.pro for it to work... it's trying to match
        //the server's hostname with the client subdomain. Once it finds a match
        //between the product and the server's hostname/subdomain, then it 
        //returns the ID of that product (aka. the service ID of an Airtime instance)
        foreach ($result["products"] as $product)
        {
            if (strpos($product[0]["groupname"], "Airtime") === FALSE)
            {
                //Ignore non-Airtime products
                continue;
            }
            else
            {
                if ($product[0]["status"] === "Active") {
                    $airtimeProduct = $product[0];
                    $subdomain = '';
        
                    foreach ($airtimeProduct['customfields']['customfield'] as $customField)
                    {
                        if ($customField['name'] === SUBDOMAIN_WHMCS_CUSTOM_FIELD_NAME)
                        {
                            $subdomain = $customField['value'];
                            if (($subdomain . ".airtime.pro") === $_SERVER['SERVER_NAME'])
                            {
                                return $airtimeProduct['id'];
                            }
                        }
                    }
                }
            }
        }
        throw new Exception("Unable to match subdomain to a service ID");
    }

    public static function getProducts()
    {
        //Making this static to cache the products during a single HTTP request. 
        //This saves us roundtrips to WHMCS if getProducts() is called multiple times.
        static $result = array();
        if (!empty($result))
        {
            return $result["products"]["product"];
        }
        
        $credentials = self::getAPICredentials();
        
        $postfields = array();
        $postfields["username"] = $credentials["username"];
        $postfields["password"] = md5($credentials["password"]);
        $postfields["action"] = "getproducts";
        $postfields["responsetype"] = "json";
        //gid is the Airtime product group id on whmcs
        $postfields["gid"] = "15";
        
        $query_string = "";
        foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";
        
        $result = self::makeRequest($credentials["url"], $query_string);
        return $result["products"]["product"];
    }
    
    public static function getProductPricesAndTypes()
    {
        $products = BillingController::getProducts();
        
        foreach ($products as $k => $p) {
            $productPrices[$p["name"]] = array(
                    "monthly" => $p["pricing"]["USD"]["monthly"],
                    "annually" => $p["pricing"]["USD"]["annually"]
            );
            $productTypes[$p["pid"]] = $p["name"] . " ($" . $productPrices[$p['name']]['monthly'] . "/mo)";
        }
        return array($productPrices, $productTypes);
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
    }
    
    private static function makeRequest($url, $query_string) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
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
    }
    
    private static function ensureClientIdIsValid()
    {
        if (Application_Model_Preference::GetClientId() == null)
        {
            throw new Exception("Invalid client ID: " . Application_Model_Preference::GetClientId());
        }
    }
}