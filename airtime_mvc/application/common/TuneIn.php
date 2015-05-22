<?php

class Application_Common_TuneIn
{
    /**
     * @param $title url encoded string
     * @param $artist url encoded string
     */
    public static function sendMetadataToTunein($title, $artist)
    {
        $credQryStr = self::getCredentialsQueryString();
        $metadataQryStr = "&title=".$title."&artist=".$artist."&commercial=false";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, TUNEIN_API_URL . $credQryStr . $metadataQryStr);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        curl_exec($ch);
        if (curl_error($ch)) {
            Logging::error("Failed to reach TuneIn: ". curl_errno($ch)." - ". curl_error($ch) . " - " . curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
        }
        curl_close($ch);

    }

    private static function getCredentialsQueryString() {
        $tuneInStationID = Application_Model_Preference::getTuneinStationId();
        $tuneInPartnerID = Application_Model_Preference::getTuneinPartnerId();
        $tuneInPartnerKey = Application_Model_Preference::getTuneinPartnerKey();

        return "?partnerId=".$tuneInPartnerID."&partnerKey=".$tuneInPartnerKey."&id=".$tuneInStationID;
    }

    public static function updateOfflineMetadata() {
        $credQryStr = self::getCredentialsQueryString();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, TUNEIN_API_URL . $credQryStr . "&commercial=true");
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        curl_exec($ch);
        if (curl_error($ch)) {
            Logging::error("Failed to reach TuneIn: ". curl_errno($ch)." - ". curl_error($ch) . " - " . curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
        }
        curl_close($ch);
    }

}
