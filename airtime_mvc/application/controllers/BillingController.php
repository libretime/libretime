<?php

class BillingController extends Zend_Controller_Action {

    public function indexAction()
    {
        
    }

    public function upgradeAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_BillingUpgradeDowngrade();
        if ($request->isPost()) {
            $formData = $request->getPost();
            
            /*$accessKey = $_SERVER["WHMCS_ACCESS_KEY"];
            $username = $_SERVER["WHMCS_USERNAME"];
            $password = $_SERVER["WHMCS_PASSWORD"];
            $url = "https://account.sourcefabric.com/includes/api.php?accesskey=" . $accessKey;*/
            $url = "https://account.sourcefabric.com/includes/api.php";
            
            $postfields = array();
            $postfields["username"] = $username;
            $postfields["password"] = md5($password);
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
            
            $query_string = "";
            foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";
            
            //$result = $this->makeRequest($url, $query_string);
            //$invoiceUrl = "https://account.sourcefabric.com/viewinvoice.php?id=".$result["invoiceid"];
            
            $whmcsurl = "https://account.sourcefabric.com/dologin.php";
            $autoauthkey = "";
            $timestamp = time(); //whmcs timezone?
            $client = self::getClientDetails();
            $email = $client["email"];
            $hash = sha1($email.$timestamp.$autoauthkey);
            $goto="viewinvoice.php?id=5108";
            $this->_redirect($whmcsurl."?email=$email&timestamp=$timestamp&hash=$hash&goto=$goto");
            
        } else {
            $this->view->form = $form;
        }
    }

    public function clientAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_BillingClient();
        if ($request->isPost()) {
            $formData = $request->getPost();
            
            /*$accessKey = $_SERVER["WHMCS_ACCESS_KEY"];
            $username = $_SERVER["WHMCS_USERNAME"];
            $password = $_SERVER["WHMCS_PASSWORD"];
            $url = "https://account.sourcefabric.com/includes/api.php?accesskey=" . $accessKey;*/
            $url = "https://account.sourcefabric.com/includes/api.php";
            
            $postfields = array();
            $postfields["username"] = $username;
            $postfields["password"] = md5($password);
            $postfields["action"] = "updateclient";
            //$postfields["clientid"] = Application_Model_Preference::GetClientId();
            $postfields["clientid"] = 1846;
            $postfields = array_merge($postfields, $formData);
            unset($postfields["password2verify"]);
            unset($postfields["submit"]);
            
            $query_string = "";
            foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";
            
            $result = $this->makeRequest($url, $query_string);
            $form = new Application_Form_BillingClient();
            $this->view->form = $form;
        } else {
            $this->view->form = $form;
        }
    }

    //TODO: this does not return a service id. why?
    private static function getClientInstanceId()
    {
        /*$accessKey = $_SERVER["WHMCS_ACCESS_KEY"];
        $username = $_SERVER["WHMCS_USERNAME"];
        $password = $_SERVER["WHMCS_PASSWORD"];
        $url = "https://account.sourcefabric.com/includes/api.php?accesskey=" . $accessKey;*/
        $url = "https://account.sourcefabric.com/includes/api.php";
        
        $postfields = array();
        $postfields["username"] = $username;
        $postfields["password"] = md5($password);
        $postfields["action"] = "getclientsproducts";
        $postfields["responsetype"] = "json";
        $postfields["clientid"] = 1846;
        //$postfields["clientid"] = Application_Model_Preference::GetClientId();
        
        $query_string = "";
        foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";
        
        $result = self::makeRequest($url, $query_string);
        Logging::info($result);
    }

    public static function getProducts()
    {
        /*$accessKey = $_SERVER["WHMCS_ACCESS_KEY"];
        $username = $_SERVER["WHMCS_USERNAME"];
        $password = $_SERVER["WHMCS_PASSWORD"];
        $url = "https://account.sourcefabric.com/includes/api.php?accesskey=" . $accessKey;*/
        $url = "https://account.sourcefabric.com/includes/api.php";
        
        $postfields = array();
        $postfields["username"] = $username;
        $postfields["password"] = md5($password);
        $postfields["action"] = "getproducts";
        $postfields["responsetype"] = "json";
        $postfields["gid"] = "15";
        
        $query_string = "";
        foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";
        
        $result = self::makeRequest($url, $query_string);
        return $result["products"]["product"];
    }

    public static function getClientDetails()
    {
        try {
            /*$accessKey = $_SERVER["WHMCS_ACCESS_KEY"];
            $username = $_SERVER["WHMCS_USERNAME"];
            $password = $_SERVER["WHMCS_PASSWORD"];
            $url = "https://account.sourcefabric.com/includes/api.php?accesskey=" . $accessKey;*/
            $url = "https://account.sourcefabric.com/includes/api.php";
            
            $postfields = array();
            $postfields["username"] = $username;
            $postfields["password"] = md5($password);
            $postfields["action"] = "getclientsdetails";
            $postfields["stats"] = true;
            //$postfields["clientid"] = Application_Model_Preference::GetClientId();
            $postfields["clientid"] = 1846;
            $postfields["responsetype"] = "json";
            
            $query_string = "";
            foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";
            
            $arr = self::makeRequest($url, $query_string);
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