<?php

define("WHMCS_API_URL", "https://account.sourcefabric.com/includes/api.php");

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
        
        $username = "admin";
        $email = $_POST["email"];
        $password = $_POST["password"];
                
        Application_Model_Locale::configureLocalization($request->getcookie('airtime_locale', 'en_CA'));
        if (Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('Showbuilder');
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

            $this->_redirect('Showbuilder');
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
        if (!$this->validateCredentialsWithWHMCS($this->email, $this->password))
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
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $jsondata = curl_exec($ch);
        if (curl_error($ch)) {
            die(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
            //die("Connection Error: ".curl_errno($ch).' - '.curl_error($ch));
        }
        curl_close($ch);
        
        $arr = json_decode($jsondata, true); # Decode JSON String
        
        if ($arr["result"] != "success") {
            return false;
        }
                
        return true;
    }
}