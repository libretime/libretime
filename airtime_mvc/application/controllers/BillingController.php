<?php

class BillingController extends Zend_Controller_Action {

    public function indexAction()
    {
        
    }

    public function purchaseAction()
    {
        $form = new Application_Form_BillingPurchase();
        $this->view->form = $form;
        //$this->view->html = $this->view->render('billing/purchase.phtml');
    }

    public static function getClientDetails()
    {
        try {
            $accessKey = $_SERVER["WHMCS_ACCESS_KEY"];
            $username = $_SERVER["WHMCS_USERNAME"];
            $password = $_SERVER["WHMCS_PASSWORD"];
            $url = "https://account.sourcefabric.com/includes/api.php?accesskey=" . $accessKey;
            
            $postfields = array();
            $postfields["username"] = $username;
            $postfields["password"] = md5($password);
            $postfields["action"] = "getclientsdetails";
            $postfields["stats"] = true;
            $postfields["clientid"] = Application_Model_Preference::GetClientId();
            $postfields["responsetype"] = "json";
            
            $query_string = "";
            foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";
            
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
            
            $arr = json_decode($jsondata, true);
            return $arr["client"];
        } catch (Exception $e) {
            Logging::info($e->getMessage());
        }
    }
}