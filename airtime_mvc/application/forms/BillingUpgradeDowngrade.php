<?php
class Application_Form_BillingUpgradeDowngrade extends Zend_Form
{
    public function init()
    {
        $productPrices = array();
        $productTypes = array();       
        list($productPrices, $productTypes) = BillingController::getProductPricesAndTypes();
               
        //$currentPlanType = ucfirst(Application_Model_Preference::GetPlanLevel());
        $currentPlanType = "Hobbyist";
        if (($key = array_search($currentPlanType, $productTypes)) !== false) {
            //unset($productTypes[$key]);
        }
        
        $currentPlanProduct = BillingController::getClientCurrentAirtimeProduct();
        $currentPlanProductId = $currentPlanProduct["pid"];
        $pid = new Zend_Form_Element_Radio('newproductid');
        $pid->setLabel(_('Plan type:'))
            ->setMultiOptions($productTypes)
            ->setRequired(true)
            ->setValue($currentPlanProductId);
        $this->addElement($pid);       
        
        Logging::info(BillingController::getClientCurrentAirtimeProduct());
        $billingcycle = new Zend_Form_Element_Radio('newproductbillingcycle');
        $billingcycle->setLabel(_('Billing cycle:'))
            ->setMultiOptions(array('monthly' => 'Monthly', 'annually' => 'Annually'))
            ->setRequired(true)
            ->setValue(BillingController::getClientCurrentAirtimeProduct()["billingcycle"]);
            
        $this->addElement($billingcycle);

        $paymentmethod = new Zend_Form_Element_Radio('paymentmethod');
        $paymentmethod->setLabel(_('Payment method:'))
            ->setRequired(true)
            ->setMultiOptions(array(
                'paypal' => _('PayPal'),
                'tco' => _('Credit Card via 2Checkout')))
            ->setValue('paypal');
        $this->addElement($paymentmethod);
        
        /*$submit = new Zend_Form_Element_Submit("submit");
        $submit->setIgnore(true)
                ->setLabel(_("Save"));
        $this->addElement($submit);*/
        
        $client = new Application_Form_BillingClient();
        $client->removeElement("password2");
        $client->removeElement("password2verify");
        $this->addSubForm($client, 'billing_client_info');
    }
}
