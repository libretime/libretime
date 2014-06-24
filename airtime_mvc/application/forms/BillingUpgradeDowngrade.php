<?php
class Application_Form_BillingUpgradeDowngrade extends Zend_Form
{
    public function init()
    {
        $productPrices = array();
        $productTypes = array();
        $products = BillingController::getProducts();
                
        foreach ($products as $k => $p) {
            $productPrices[$p["name"]] = array(
                "monthly" => $p["pricing"]["USD"]["monthly"],
                "annualy" => $p["pricing"]["USD"]["annually"]
            );
            $productTypes[$p["pid"]] = $p["name"];
        }
        
        //$currentPlanType = ucfirst(Application_Model_Preference::GetPlanLevel());
        $currentPlanType = "Hobbyist";
        if (($key = array_search($currentPlanType, $productTypes)) !== false) {
            //unset($productTypes[$key]);
        }
        
        $pid = new Zend_Form_Element_Radio('newproductid');
        $pid->setLabel(_('Plan type:'))
            ->setMultiOptions($productTypes)
            ->setRequired(true)
            ->setValue(26);
        $this->addElement($pid);       
        
        $billingcycle = new Zend_Form_Element_Radio('newproductbillingcycle');
        $billingcycle->setLabel(_('Billing cycle:'))
            ->setMultiOptions(array('monthly' => 'monthly', 'annually' => 'annually'))
            ->setRequired(true)
            ->setValue('monthly');
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
        $this->addSubForm($client, 'billing_client_info');
    }
}
