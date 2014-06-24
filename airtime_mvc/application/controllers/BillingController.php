<?php

class BillingController extends Zend_Controller_Action {

    public function indexAction()
    {
        
    }

    public function upgradeAction()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headLink()->appendStylesheet($baseUrl.'css/billing.css?'.$CC_CONFIG['airtime_version']);
        
        $request = $this->getRequest();
        $form = new Application_Form_BillingUpgradeDowngrade();
        if ($request->isPost()) {
            //$formData = $form->getValues();
            $formData = $request->getPost();
            if ($form->isValid($formData)) {
                $credentials = self::getAPICredentials();
                
                $postfields = array();
                $postfields["username"] = $credentials["username"];
                $postfields["password"] = md5($credentials["password"]);
                $postfields["action"] = "upgradeproduct";
                //$postfields["clientid"] = Application_Model_Preference::GetClientId();
                $postfields["clientid"] = 1846;
                //TODO: do not hardcode
                //$postfields["serviceid"] = self::getClientInstanceId();
                $postfields["serviceid"] = "1678";
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
                //$clientfields["clientid"] = Application_Model_Preference::GetClientId();
                $clientfields["clientid"] = 1846;
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
                    //$result = $this->makeRequest($credentials["url"], $upgrade_query_string);
                    //self::viewInvoice($result["invoiceid"]);
                    self::viewInvoice(5108);
                }
            } else {
                $this->view->form = $form;
            }
        } else {
            $this->view->form = $form;
        }
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
        $request = $this->getRequest();
        $form = new Application_Form_BillingClient();
        if ($request->isPost()) {
            $formData = $request->getPost();
            if ($form->isValid($formData)) {
            
                $credentials = self::getAPICredentials();
                
                $postfields = array();
                $postfields["username"] = $credentials["username"];
                $postfields["password"] = md5($credentials["password"]);
                $postfields["action"] = "updateclient";
                //$postfields["clientid"] = Application_Model_Preference::GetClientId();
                $postfields["clientid"] = 1846;
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
        $credentials = self::getAPICredentials();
        
        $postfields = array();
        $postfields["username"] = $credentials["username"];
        $postfields["password"] = md5($credentials["password"]);
        $postfields["action"] = "getinvoices";
        $postfields["responsetype"] = "json";
        $postfields["userid"] = 1846;
        //$postfields["clientid"] = Application_Model_Preference::GetClientId();
        
        $query_string = "";
        foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";
        
        $result = self::makeRequest($credentials["url"], $query_string);
        $this->view->invoices = $result["invoices"]["invoice"];
    }
    
    public function invoiceAction()
    {
        $request = $this->getRequest();
        $invoice_id = $request->getParam('invoiceid');
        self::viewInvoice($invoice_id);
    }

    //TODO: this does not return a service id. why?
    private static function getClientInstanceId()
    {
        $credentials = self::getAPICredentials();
        
        $postfields = array();
        $postfields["username"] = $credentials["username"];
        $postfields["password"] = md5($credentials["password"]);
        $postfields["action"] = "getclientsproducts";
        $postfields["responsetype"] = "json";
        $postfields["clientid"] = 1846;
        //$postfields["clientid"] = Application_Model_Preference::GetClientId();
        
        $query_string = "";
        foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";
        
        $result = self::makeRequest($credentials["url"], $query_string);
        Logging::info($result);
    }

    public static function getProducts()
    {
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

    public static function getClientDetails()
    {
        try {
            $credentials = self::getAPICredentials();
            
            $postfields = array();
            $postfields["username"] = $credentials["username"];
            $postfields["password"] = md5($credentials["password"]);
            $postfields["action"] = "getclientsdetails";
            $postfields["stats"] = true;
            //$postfields["clientid"] = Application_Model_Preference::GetClientId();
            $postfields["clientid"] = 1846;
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
}