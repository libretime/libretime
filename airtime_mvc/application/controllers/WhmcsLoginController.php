<?php

class WhmcsLoginController extends Zend_Controller_Action
{

    public function init()
    {
    }
    
    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();

        $request = $this->getRequest();
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $username = "admin"; //This is just for appearance in your session. It shows up in the corner of the Airtime UI.
        $email = $_POST["email"];
        $password = $_POST["password"];
                
        Application_Model_Locale::configureLocalization($request->getcookie('airtime_locale', 'en_CA'));
        if (Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('showbuilder');
        }
        
        $authAdapter = new WHMCS_Auth_Adapter($username, $email, $password);
                
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        if ($result->isValid()) {
            //all info about this user from the login table omit only the password
            //$userInfo = $authAdapter->getResultRowObject(null, 'password');
        
            //the default storage is a session with namespace Zend_Auth            
            /*
            [id] => 1
            [login] => admin
            [pass] => hashed password
            [type] => A
            [first_name] =>
            [last_name] =>
            [lastlogin] =>
            [lastfail] =>
            [skype_contact] =>
            [jabber_contact] =>
            [email] => asdfasdf@asdasdf.com
            [cell_phone] =>
            [login_attempts] => 0
            */
            
            //Zend_Auth already does this for us, it's not needed:
            //$authStorage = $auth->getStorage();
            //$authStorage->write($result->getIdentity()); //$userInfo);
            
            //set the user locale in case user changed it in when logging in
            //$locale = $form->getValue('locale');
            //Application_Model_Preference::SetUserLocale($locale);

            $this->_redirect('showbuilder');
        }     
        else {
            echo("Sorry, that username or password was incorrect.");
        } 
       
        return;
    }
}

class WHMCS_Auth_Adapter implements Zend_Auth_Adapter_Interface {
    private $username;
    private $password;
    private $email;

    function __construct($username, $email, $password) {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->identity = null;
    }

    function authenticate() {        
        list($credentialsValid, $clientId) = $this->validateCredentialsWithWHMCS($this->email, $this->password);
        if (!$credentialsValid)
        {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
        }
        if (!$this->verifyClientSubdomainOwnership($clientId))
        {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, null);
        }
    
        $identity = array();
        
        //TODO: Get identity of the first admin user!
        
        /*
        $identity["id"] = 1;
        $identity["type"] = "S";
        $identity["login"] = $this->username; //admin";
        $identity["email"] = $this->email;*/
        $identity = $this->getSuperAdminIdentity();
        if (is_null($identity)) {
            Logging::error("No super admin user found");
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null);
        }
        $identity = (object)$identity; //Convert the array into an stdClass object
        
        try {
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $identity);
        } catch (Exception $e) {
            // exception occured
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null);
        }
    }
    
    private function getSuperAdminIdentity()
    {
        $firstSuperAdminUser = CcSubjsQuery::create()
        ->filterByDbType('S')
        ->orderByDbId()
        ->findOne();
        if (!$firstSuperAdminUser) {
            //If there's no super admin users, get the first regular admin user!
            $firstSuperAdminUser = CcSubjsQuery::create()
                                ->filterByDbType('A')
                                ->orderByDbId()
                                ->findOne();
            if (!$firstSuperAdminUser) {
                return null;
            }
        }
        $identity["id"] = $firstSuperAdminUser->getDbId();
        $identity["type"] = "S"; //Super Admin
        $identity["login"] = $firstSuperAdminUser->getDbLogin();
        $identity["email"] = $this->email;
        return $identity;
    }
    
    //Returns an array! Read the code carefully:
    private function validateCredentialsWithWHMCS($email, $password)
    {
        $client_postfields = array();
        $client_postfields["username"] = $_SERVER['WHMCS_USERNAME']; //WHMCS API username
        $client_postfields["password"] = md5($_SERVER['WHMCS_PASSWORD']); //WHMCS API password
        $client_postfields["action"] ="validatelogin";
        $client_postfields["responsetype"] = "json";
        
        $client_postfields["email"] = $email;
        $client_postfields["password2"] = $password;
        
        $query_string = "";
        foreach ($client_postfields as $k => $v) $query_string .= "$k=".urlencode($v)."&";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, WHMCS_API_URL);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); // WHMCS IP whitelist doesn't support IPv6
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $jsondata = curl_exec($ch);
        if (curl_error($ch)) {
            Logging::error("Failed to reach WHMCS server in " . __FUNCTION__ . ": "
                            . curl_errno($ch) . ' - ' . curl_error($ch) . ' - ' . curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
            //die("Connection Error: ".curl_errno($ch).' - '.curl_error($ch));
        }
        curl_close($ch);
        
        $arr = json_decode($jsondata, true); # Decode JSON String
        
        if ($arr["result"] != "success") {
            return array(false, -1);
        }
        $clientId = $arr["userid"];
                
        return array(true, $clientId);
    }
    
    function verifyClientSubdomainOwnership($clientId)
    {
        //Do a quick safety check to ensure the client ID we're authenticating
        //matches up to the owner of this instance.
        if ($clientId != Application_Model_Preference::GetClientId())
        {
            return false; 
        }
        $client_postfields = array();
        $client_postfields["username"] = $_SERVER['WHMCS_USERNAME'];
        $client_postfields["password"] = md5($_SERVER['WHMCS_PASSWORD']);
        $client_postfields["action"] ="getclientsproducts";
        $client_postfields["responsetype"] = "json";
    
        $client_postfields["clientid"] = $clientId;
        //$client_postfields["stats"] = "true";
    
        $query_string = "";
        foreach ($client_postfields as $k => $v) $query_string .= "$k=".urlencode($v)."&";
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, WHMCS_API_URL);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); // WHMCS IP whitelist doesn't support IPv6
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $jsondata = curl_exec($ch);
        if (curl_error($ch)) {
            Logging::error("Failed to reach WHMCS server in " . __FUNCTION__ . ": "
                            . curl_errno($ch) . ' - ' . curl_error($ch) . ' - ' . curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
            //die("Connection Error: ".curl_errno($ch).' - '.curl_error($ch));
        }
        curl_close($ch);
    
        $arr = json_decode($jsondata, true); # Decode JSON String
        //$client_id = $arr["clientid"];
        //print_r($arr);
        if ($arr["result"] != "success") {
            die("Sorry, that email address or password was incorrect.");
        }
    
        $doesAirtimeProductExist = false;
        $isAirtimeAccountSuspended = true;
        $airtimeProduct = null;
    
        foreach ($arr["products"]["product"] as $product)
        {
            if (strpos($product["groupname"], "Airtime") === FALSE)
            {
                //Ignore non-Airtime products
                continue;
            }
            else
            {
                if (($product["status"] === "Active") || ($product["status"] === "Suspended")) {
                    $airtimeProduct = $product;
                    $subdomain = '';

                    foreach ($airtimeProduct['customfields']['customfield'] as $customField)
                    {
                        if ($customField['name'] === SUBDOMAIN_WHMCS_CUSTOM_FIELD_NAME)
                        {
                            $subdomain = $customField['value'];
                            if (($subdomain . ".airtime.pro") === $_SERVER['SERVER_NAME'])
                            {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
}
