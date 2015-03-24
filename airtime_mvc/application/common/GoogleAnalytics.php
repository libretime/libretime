<?php

class Application_Common_GoogleAnalytics
{

    /** Returns a string containing the JavaScript code to pass some billing account info
     *  into Google Tag Manager / Google Analytics, so we can track things like the plan type.
     */
    public static function generateGoogleTagManagerDataLayerJavaScript()
    {
        $code = "";

        try {
            $clientId = Application_Model_Preference::GetClientId();

            $plan = Application_Model_Preference::GetPlanLevel();
            $isTrial = ($plan == "trial");

            //Figure out how long the customer has been around using a mega hack.
            //(I'm avoiding another round trip to WHMCS for now...)
            //We calculate it based on the trial end date...
            $trialEndDateStr = Application_Model_Preference::GetTrialEndingDate();
            if ($trialEndDateStr == '') {
                $accountDuration = 0;
            } else {
                $today = new DateTime();
                $trialEndDate = new DateTime($trialEndDateStr);
                $trialDuration = new DateInterval("P30D"); //30 day trial duration
                $accountCreationDate = $trialEndDate->sub($trialDuration);
                $interval = $today->diff($accountCreationDate);
                $accountDuration = $interval->days;
            }

            $code = "$( document ).ready(function() {
                    dataLayer.push({
                                    'UserID':  '" . $clientId . "',
                                    'Customer':  'Customer',
                                    'PlanType':  '" . $plan . "',
                                    'Trial':  '" . $isTrial . "',
                                    'AccountDuration':  '" . strval($accountDuration) . "'
                                    });
                     });";
            //No longer sending these variables because we used to make a query to WHMCS
            //to fetch them, which was slow.
            //               'ZipCode':  '" . $postcode . "',
            //               'Country':  '" . $country . "',

        } catch (Exception $e) {
            Logging::error($e);
            return "";
        }
        return $code;
    }

    /** Generate the JavaScript snippet that logs a trial to paid conversion  */
    public static function generateConversionTrackingJavaScript()
    {
        $newPlan = Application_Model_Preference::GetPlanLevel();
        $oldPlan = Application_Model_Preference::GetOldPlanLevel();

        $code = "dataLayer.push({'event': 'Conversion',
                                 'Conversion': 'Trial to Paid',
                                 'Old Plan' : '$oldPlan',
                                 'New Plan' : '$newPlan'});";
        return $code;
    }

    /** Return true if the user used to be on a trial plan and was just converted to a paid plan. */
    public static function didPaidConversionOccur($request)
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        if ($userInfo) {
            $user = new Application_Model_User($userInfo->id);
        } else {
            return;
        }

        $oldPlan = Application_Model_Preference::GetOldPlanLevel();

        if ($user->isSuperAdmin() &&
            !$user->isSourcefabricAdmin() &&
            $request->getControllerKey() !== "thank-you")
        {
            //Only tracking trial->paid conversions for now.
            if ($oldPlan == "trial")
            {
                return true;
            }
        }
        return false;
    }
}