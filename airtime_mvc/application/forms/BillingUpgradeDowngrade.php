<?php
class Application_Form_BillingUpgradeDowngrade extends Zend_Form
{
    public function init()
    {
        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrf_element = new Zend_Form_Element_Hidden('csrf');
        $csrf_element->setValue($csrf_namespace->authtoken)->setRequired('true')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $this->addElement($csrf_element);

        $productPrices = array();
        $productTypes = array();       
        list($productPrices, $productTypes) = Billing::getProductPricesAndTypes();
                       
        $currentPlanProduct = Billing::getClientCurrentAirtimeProduct();
        $currentPlanProductId = $currentPlanProduct["pid"];
        
        $currentPlanProductBillingCycle = $currentPlanProduct["billingcycle"];
        $pid = new Zend_Form_Element_Radio('newproductid');
        $pid->setLabel(_('Plan type:'))
            ->setMultiOptions($productTypes)
            ->setRequired(true)
            ->setValue($currentPlanProductId);
        $this->addElement($pid);       
        
        //Logging::info(BillingController::getClientCurrentAirtimeProduct());
        $billingcycle = new Zend_Form_Element_Radio('newproductbillingcycle');
        $billingCycleOptionMap = array('monthly' => 'Monthly', 'annually' => 'Annually');
        if (!array_key_exists($currentPlanProductBillingCycle, $billingCycleOptionMap)) {
            $currentPlanProductBillingCycle = 'monthly';
        }
        $billingcycle->setLabel(_('Billing cycle:'))
            ->setMultiOptions($billingCycleOptionMap)
            ->setRequired(true)
            ->setValue($currentPlanProductBillingCycle);
            
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
