<?php

require_once('Billing.php');
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
        $this->_redirect('billing/upgrade');
    }

    public function upgradeAction()
    {

        Zend_Layout::getMvcInstance()->assign('parent_page', 'Billing');

        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headLink()->appendStylesheet($baseUrl.'css/billing.css?'.$CC_CONFIG['airtime_version']);
        Billing::ensureClientIdIsValid();
        
        $request = $this->getRequest();
        $form = new Application_Form_BillingUpgradeDowngrade();

        if ($request->isPost()) {
                        
            $formData = $request->getPost();

            if ($form->isValid($formData)) {

                $credentials = Billing::getAPICredentials();

                //Check if VAT should be applied or not to this invoice.
                if (in_array("7", $formData["customfields"])) {
                    $apply_vat = Billing::checkIfVatShouldBeApplied($formData["customfields"]["7"], $formData["country"]);
                } else {
                    $apply_vat = false;
                }

                $placeAnUpgradeOrder = true;

                $currentPlanProduct = Billing::getClientCurrentAirtimeProduct();
                $currentPlanProductId = $currentPlanProduct["pid"];
                $currentPlanProductBillingCycle = strtolower($currentPlanProduct["billingcycle"]);
                //If there's been no change in the plan or the billing cycle, we should not
                //place an upgrade order. WHMCS doesn't allow this in its web interface,
                //and it freaks out and does the wrong thing if we do it via the API
                //so we have to do avoid that.
                if (($currentPlanProductId == $formData["newproductid"]) &&
                    ($currentPlanProductBillingCycle == $formData["newproductbillingcycle"])
                ) {
                    $placeAnUpgradeOrder = false;
                }

                $postfields = array();
                $postfields["username"] = $credentials["username"];
                $postfields["password"] = md5($credentials["password"]);
                $postfields["action"] = "upgradeproduct";
                $postfields["clientid"] = Application_Model_Preference::GetClientId();

                $postfields["serviceid"] = Billing::getClientInstanceId();
                $postfields["type"] = "product";
                $postfields["newproductid"] = $formData["newproductid"];
                $postfields["newproductbillingcycle"] = $formData["newproductbillingcycle"];
                $postfields["paymentmethod"] = $formData["paymentmethod"];
                $postfields["responsetype"] = "json";

                $upgrade_query_string = "";
                foreach ($postfields AS $k => $v) $upgrade_query_string .= "$k=" . urlencode($v) . "&";

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
                foreach ($clientfields AS $k => $v) $client_query_string .= "$k=" . urlencode($v) . "&";

                //Update the client details in WHMCS first
                $result = Billing::makeRequest($credentials["url"], $client_query_string);
                Logging::info($result);
                if ($result["result"] == "error") {
                    $this->setErrorMessage();
                    $this->view->form = $form;
                    return;
                }

                //If there were no changes to the plan or billing cycle, we just redirect you to the
                //invoices screen and show a message.
                if (!$placeAnUpgradeOrder) {
                    $this->_redirect('billing/invoices?planupdated');
                    return;
                }

                //Then place an upgrade order in WHMCS
                $result = Billing::makeRequest($credentials["url"], $upgrade_query_string);
                if ($result["result"] == "error") {
                    Logging::info($_SERVER['HTTP_HOST'] . " - Account upgrade failed. - " . $result["message"]);
                    $this->setErrorMessage();
                    $this->view->form = $form;
                } else {
                    Logging::info($_SERVER['HTTP_HOST'] . "Account plan upgrade request:");
                    Logging::info($result);

                    // Disable the view and the layout here, squashes an error.
                    $this->view->layout()->disableLayout();
                    $this->_helper->viewRenderer->setNoRender(true);

                    if ($apply_vat) {
                        Billing::addVatToInvoice($result["invoiceid"]);
                    }

                    // there may not be an invoice created if the client is downgrading
                    if (!empty($result["invoiceid"])) {
                        self::viewInvoice($result["invoiceid"]);
                    } else {
                        $this->_redirect('billing/invoices?planupdated');
                        return;
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
        $this->_helper->json(array("result"=>Billing::isCountryInEU($formData["country"])));
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
        $this->_helper->json(array("result"=>Billing::checkIfVatShouldBeApplied($vatNumber, $formData["country"])));
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

    private static function viewInvoice($invoice_id)
    {
        $whmcsurl = "https://account.sourcefabric.com/dologin.php";
        $autoauthkey = $_SERVER["WHMCS_AUTOAUTH_KEY"];
        $timestamp = time(); //whmcs timezone?
        $client = Billing::getClientDetails();
        $email = $client["email"];
        $hash = sha1($email.$timestamp.$autoauthkey);
        $goto = "viewinvoice.php?id=".$invoice_id;
        header("Location: ".$whmcsurl."?email=$email&timestamp=$timestamp&hash=$hash&goto=$goto");
    }

    public function clientAction()
    {
        Zend_Layout::getMvcInstance()->assign('parent_page', 'Billing');

        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headLink()->appendStylesheet($baseUrl.'css/billing.css?'.$CC_CONFIG['airtime_version']);
        
        $request = $this->getRequest();
        $form = new Application_Form_BillingClient();
        Billing::ensureClientIdIsValid();
        if ($request->isPost()) {
            $formData = $request->getPost();
            if ($form->isValid($formData)) {
            
                $credentials = Billing::getAPICredentials();
                
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
                
                $result = Billing::makeRequest($credentials["url"], $query_string);

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
        Zend_Layout::getMvcInstance()->assign('parent_page', 'Billing');

        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headLink()->appendStylesheet($baseUrl.'css/billing.css?'.$CC_CONFIG['airtime_version']);

        Billing::ensureClientIdIsValid();
        $credentials = Billing::getAPICredentials();
        
        $postfields = array();
        $postfields["username"] = $credentials["username"];
        $postfields["password"] = md5($credentials["password"]);
        $postfields["action"] = "getinvoices";
        $postfields["responsetype"] = "json";
        $postfields["userid"] = Application_Model_Preference::GetClientId();
        
        $query_string = "";
        foreach ($postfields AS $k=>$v) $query_string .= "$k=".urlencode($v)."&";
        
        $result = Billing::makeRequest($credentials["url"], $query_string);
        
        if ($result["invoices"]) {
            $this->view->invoices = $result["invoices"]["invoice"];;
        } else {
            $this->view->invoices = array();
        }
    }
    
    public function invoiceAction()
    {
        Billing::ensureClientIdIsValid();
        $request = $this->getRequest();
        $invoice_id = $request->getParam('invoiceid');
        self::viewInvoice($invoice_id);
    }


}
