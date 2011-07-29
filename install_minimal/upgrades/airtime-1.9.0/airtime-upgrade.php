<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

set_include_path(__DIR__.'/../../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/library/pear' . PATH_SEPARATOR . get_include_path());
set_include_path(__DIR__.'/../../../airtime_mvc/application/models' . PATH_SEPARATOR . get_include_path());
require_once 'conf.php';
require_once 'DB.php';

require_once 'propel/runtime/lib/Propel.php';
Propel::init(__DIR__."/../../../airtime_mvc/application/configs/airtime-conf.php");

const CONF_DIR_BINARIES = "/usr/lib/airtime";

class AirtimeInstall{

    const CONF_DIR_LOG = "/var/log/airtime";

    public static function CreateZendPhpLogFile(){
        global $CC_CONFIG;

        echo "* Creating logs directory ".AirtimeInstall::CONF_DIR_LOG.PHP_EOL;

        $path = AirtimeInstall::CONF_DIR_LOG;
        $file = $path.'/zendphp.log';
        if (!file_exists($path)){
            mkdir($path, 0755, true);
        }

        touch($file);
        chmod($file, 0755);
        chown($file, $CC_CONFIG['webServerUser']);
        chgrp($file, $CC_CONFIG['webServerUser']);
    }

    public static function CreateSymlinksToUtils()
    {
        echo "* Creating /usr/bin symlinks".PHP_EOL;
        AirtimeInstall::RemoveSymlinks();

        echo "* Installing airtime-import".PHP_EOL;
        $dir = CONF_DIR_BINARIES."/utils/airtime-import/airtime-import";
        exec("ln -s $dir /usr/bin/airtime-import");

        echo "* Installing airtime-update-db-settings".PHP_EOL;
        $dir = CONF_DIR_BINARIES."/utils/airtime-update-db-settings";
        exec("ln -s $dir /usr/bin/airtime-update-db-settings");

        echo "* Installing airtime-check-system".PHP_EOL;
        $dir = CONF_DIR_BINARIES."/utils/airtime-check-system";
        exec("ln -s $dir /usr/bin/airtime-check-system");
    }

    public static function RemoveSymlinks()
    {
        exec("rm -f /usr/bin/airtime-import");
        exec("rm -f /usr/bin/airtime-update-db-settings");
        exec("rm -f /usr/bin/airtime-check-system");
    }

    public static function DbTableExists($p_name)
    {
        global $CC_DBC;
        $sql = "SELECT * FROM ".$p_name;
        $result = $CC_DBC->GetOne($sql);
        if (PEAR::isError($result)) {
            return false;
        }
        return true;
    }

    public static function BypassMigrations($dir, $version)
    {
        $appDir = AirtimeInstall::GetAirtimeSrcDir();
        $command = "php $appDir/library/doctrine/migrations/doctrine-migrations.phar ".
                    "--configuration=$dir/../../DoctrineMigrations/migrations.xml ".
                    "--db-configuration=$appDir/library/doctrine/migrations/migrations-db.php ".
                    "--no-interaction --add migrations:version $version";
        system($command);
    }

    public static function MigrateTablesToVersion($dir, $version)
    {
        $appDir = AirtimeInstall::GetAirtimeSrcDir();
        $command = "php $appDir/library/doctrine/migrations/doctrine-migrations.phar ".
                    "--configuration=$dir/../../DoctrineMigrations/migrations.xml ".
                    "--db-configuration=$appDir/library/doctrine/migrations/migrations-db.php ".
                    "--no-interaction migrations:migrate $version";
        system($command);
    }

    public static function CreateCronFile(){
        // Create CRON task to run every day.  Time of day is initialized to a random time.
        $hour = rand(0,23);
        $minute = rand(0,59);

        $fp = fopen('/etc/cron.d/airtime-crons','w');
        fwrite($fp, "$minute $hour * * * root /usr/lib/airtime/utils/phone_home_stat\n");
        fclose($fp);
    }

    public static function GetAirtimeSrcDir()
    {
        return __DIR__."/../../../airtime_mvc";
    }

    public static function InsertCountryDataIntoDatabase(){
        $sql = "INSERT INTO cc_country (isocode, name) VALUES ('AFG', 'Afghanistan ');
        INSERT INTO cc_country (isocode, name) VALUES ('ALA', 'Åland Islands');
        INSERT INTO cc_country (isocode, name) VALUES ('ALB', 'Albania ');
        INSERT INTO cc_country (isocode, name) VALUES ('DZA', 'Algeria ');
        INSERT INTO cc_country (isocode, name) VALUES ('ASM', 'American Samoa ');
        INSERT INTO cc_country (isocode, name) VALUES ('AND', 'Andorra ');
        INSERT INTO cc_country (isocode, name) VALUES ('AGO', 'Angola ');
        INSERT INTO cc_country (isocode, name) VALUES ('AIA', 'Anguilla ');
        INSERT INTO cc_country (isocode, name) VALUES ('ATG', 'Antigua and Barbuda ');
        INSERT INTO cc_country (isocode, name) VALUES ('ARG', 'Argentina ');
        INSERT INTO cc_country (isocode, name) VALUES ('ARM', 'Armenia ');
        INSERT INTO cc_country (isocode, name) VALUES ('ABW', 'Aruba ');
        INSERT INTO cc_country (isocode, name) VALUES ('AUS', 'Australia ');
        INSERT INTO cc_country (isocode, name) VALUES ('AUT', 'Austria ');
        INSERT INTO cc_country (isocode, name) VALUES ('AZE', 'Azerbaijan ');
        INSERT INTO cc_country (isocode, name) VALUES ('BHS', 'Bahamas ');
        INSERT INTO cc_country (isocode, name) VALUES ('BHR', 'Bahrain ');
        INSERT INTO cc_country (isocode, name) VALUES ('BGD', 'Bangladesh ');
        INSERT INTO cc_country (isocode, name) VALUES ('BRB', 'Barbados ');
        INSERT INTO cc_country (isocode, name) VALUES ('BLR', 'Belarus ');
        INSERT INTO cc_country (isocode, name) VALUES ('BEL', 'Belgium ');
        INSERT INTO cc_country (isocode, name) VALUES ('BLZ', 'Belize ');
        INSERT INTO cc_country (isocode, name) VALUES ('BEN', 'Benin ');
        INSERT INTO cc_country (isocode, name) VALUES ('BMU', 'Bermuda ');
        INSERT INTO cc_country (isocode, name) VALUES ('BTN', 'Bhutan ');
        INSERT INTO cc_country (isocode, name) VALUES ('BOL', 'Bolivia (Plurinational State of) ');
        INSERT INTO cc_country (isocode, name) VALUES ('BES', 'Bonaire, Saint Eustatius and Saba');
        INSERT INTO cc_country (isocode, name) VALUES ('BIH', 'Bosnia and Herzegovina ');
        INSERT INTO cc_country (isocode, name) VALUES ('BWA', 'Botswana ');
        INSERT INTO cc_country (isocode, name) VALUES ('BRA', 'Brazil ');
        INSERT INTO cc_country (isocode, name) VALUES ('VGB', 'British Virgin Islands ');
        INSERT INTO cc_country (isocode, name) VALUES ('BRN', 'Brunei Darussalam ');
        INSERT INTO cc_country (isocode, name) VALUES ('BGR', 'Bulgaria ');
        INSERT INTO cc_country (isocode, name) VALUES ('BFA', 'Burkina Faso ');
        INSERT INTO cc_country (isocode, name) VALUES ('BDI', 'Burundi ');
        INSERT INTO cc_country (isocode, name) VALUES ('KHM', 'Cambodia ');
        INSERT INTO cc_country (isocode, name) VALUES ('CMR', 'Cameroon ');
        INSERT INTO cc_country (isocode, name) VALUES ('CAN', 'Canada ');
        INSERT INTO cc_country (isocode, name) VALUES ('CPV', 'Cape Verde ');
        INSERT INTO cc_country (isocode, name) VALUES ('CYM', 'Cayman Islands ');
        INSERT INTO cc_country (isocode, name) VALUES ('CAF', 'Central African Republic ');
        INSERT INTO cc_country (isocode, name) VALUES ('TCD', 'Chad ');
        INSERT INTO cc_country (isocode, name) VALUES ('CHL', 'Chile ');
        INSERT INTO cc_country (isocode, name) VALUES ('CHN', 'China ');
        INSERT INTO cc_country (isocode, name) VALUES ('HKG', 'China, Hong Kong Special Administrative Region');
        INSERT INTO cc_country (isocode, name) VALUES ('MAC', 'China, Macao Special Administrative Region');
        INSERT INTO cc_country (isocode, name) VALUES ('COL', 'Colombia ');
        INSERT INTO cc_country (isocode, name) VALUES ('COM', 'Comoros ');
        INSERT INTO cc_country (isocode, name) VALUES ('COG', 'Congo ');
        INSERT INTO cc_country (isocode, name) VALUES ('COK', 'Cook Islands ');
        INSERT INTO cc_country (isocode, name) VALUES ('CRI', 'Costa Rica ');
        INSERT INTO cc_country (isocode, name) VALUES ('CIV', 'Côte d''Ivoire ');
        INSERT INTO cc_country (isocode, name) VALUES ('HRV', 'Croatia ');
        INSERT INTO cc_country (isocode, name) VALUES ('CUB', 'Cuba ');
        INSERT INTO cc_country (isocode, name) VALUES ('CUW', 'Curaçao');
        INSERT INTO cc_country (isocode, name) VALUES ('CYP', 'Cyprus ');
        INSERT INTO cc_country (isocode, name) VALUES ('CZE', 'Czech Republic ');
        INSERT INTO cc_country (isocode, name) VALUES ('PRK', 'Democratic People''s Republic of Korea ');
        INSERT INTO cc_country (isocode, name) VALUES ('COD', 'Democratic Republic of the Congo ');
        INSERT INTO cc_country (isocode, name) VALUES ('DNK', 'Denmark ');
        INSERT INTO cc_country (isocode, name) VALUES ('DJI', 'Djibouti ');
        INSERT INTO cc_country (isocode, name) VALUES ('DMA', 'Dominica ');
        INSERT INTO cc_country (isocode, name) VALUES ('DOM', 'Dominican Republic ');
        INSERT INTO cc_country (isocode, name) VALUES ('ECU', 'Ecuador ');
        INSERT INTO cc_country (isocode, name) VALUES ('EGY', 'Egypt ');
        INSERT INTO cc_country (isocode, name) VALUES ('SLV', 'El Salvador ');
        INSERT INTO cc_country (isocode, name) VALUES ('GNQ', 'Equatorial Guinea ');
        INSERT INTO cc_country (isocode, name) VALUES ('ERI', 'Eritrea ');
        INSERT INTO cc_country (isocode, name) VALUES ('EST', 'Estonia ');
        INSERT INTO cc_country (isocode, name) VALUES ('ETH', 'Ethiopia ');
        INSERT INTO cc_country (isocode, name) VALUES ('FRO', 'Faeroe Islands ');
        INSERT INTO cc_country (isocode, name) VALUES ('FLK', 'Falkland Islands (Malvinas) ');
        INSERT INTO cc_country (isocode, name) VALUES ('FJI', 'Fiji ');
        INSERT INTO cc_country (isocode, name) VALUES ('FIN', 'Finland ');
        INSERT INTO cc_country (isocode, name) VALUES ('FRA', 'France ');
        INSERT INTO cc_country (isocode, name) VALUES ('GUF', 'French Guiana ');
        INSERT INTO cc_country (isocode, name) VALUES ('PYF', 'French Polynesia ');
        INSERT INTO cc_country (isocode, name) VALUES ('GAB', 'Gabon ');
        INSERT INTO cc_country (isocode, name) VALUES ('GMB', 'Gambia ');
        INSERT INTO cc_country (isocode, name) VALUES ('GEO', 'Georgia ');
        INSERT INTO cc_country (isocode, name) VALUES ('DEU', 'Germany ');
        INSERT INTO cc_country (isocode, name) VALUES ('GHA', 'Ghana ');
        INSERT INTO cc_country (isocode, name) VALUES ('GIB', 'Gibraltar ');
        INSERT INTO cc_country (isocode, name) VALUES ('GRC', 'Greece ');
        INSERT INTO cc_country (isocode, name) VALUES ('GRL', 'Greenland ');
        INSERT INTO cc_country (isocode, name) VALUES ('GRD', 'Grenada ');
        INSERT INTO cc_country (isocode, name) VALUES ('GLP', 'Guadeloupe ');
        INSERT INTO cc_country (isocode, name) VALUES ('GUM', 'Guam ');
        INSERT INTO cc_country (isocode, name) VALUES ('GTM', 'Guatemala ');
        INSERT INTO cc_country (isocode, name) VALUES ('GGY', 'Guernsey');
        INSERT INTO cc_country (isocode, name) VALUES ('GIN', 'Guinea ');
        INSERT INTO cc_country (isocode, name) VALUES ('GNB', 'Guinea-Bissau ');
        INSERT INTO cc_country (isocode, name) VALUES ('GUY', 'Guyana ');
        INSERT INTO cc_country (isocode, name) VALUES ('HTI', 'Haiti ');
        INSERT INTO cc_country (isocode, name) VALUES ('VAT', 'Holy See ');
        INSERT INTO cc_country (isocode, name) VALUES ('HND', 'Honduras ');
        INSERT INTO cc_country (isocode, name) VALUES ('HUN', 'Hungary ');
        INSERT INTO cc_country (isocode, name) VALUES ('ISL', 'Iceland ');
        INSERT INTO cc_country (isocode, name) VALUES ('IND', 'India ');
        INSERT INTO cc_country (isocode, name) VALUES ('IDN', 'Indonesia ');
        INSERT INTO cc_country (isocode, name) VALUES ('IRN', 'Iran (Islamic Republic of)');
        INSERT INTO cc_country (isocode, name) VALUES ('IRQ', 'Iraq ');
        INSERT INTO cc_country (isocode, name) VALUES ('IRL', 'Ireland ');
        INSERT INTO cc_country (isocode, name) VALUES ('IMN', 'Isle of Man ');
        INSERT INTO cc_country (isocode, name) VALUES ('ISR', 'Israel ');
        INSERT INTO cc_country (isocode, name) VALUES ('ITA', 'Italy ');
        INSERT INTO cc_country (isocode, name) VALUES ('JAM', 'Jamaica ');
        INSERT INTO cc_country (isocode, name) VALUES ('JPN', 'Japan ');
        INSERT INTO cc_country (isocode, name) VALUES ('JEY', 'Jersey');
        INSERT INTO cc_country (isocode, name) VALUES ('JOR', 'Jordan ');
        INSERT INTO cc_country (isocode, name) VALUES ('KAZ', 'Kazakhstan ');
        INSERT INTO cc_country (isocode, name) VALUES ('KEN', 'Kenya ');
        INSERT INTO cc_country (isocode, name) VALUES ('KIR', 'Kiribati ');
        INSERT INTO cc_country (isocode, name) VALUES ('KWT', 'Kuwait ');
        INSERT INTO cc_country (isocode, name) VALUES ('KGZ', 'Kyrgyzstan ');
        INSERT INTO cc_country (isocode, name) VALUES ('LAO', 'Lao People''s Democratic Republic ');
        INSERT INTO cc_country (isocode, name) VALUES ('LVA', 'Latvia ');
        INSERT INTO cc_country (isocode, name) VALUES ('LBN', 'Lebanon ');
        INSERT INTO cc_country (isocode, name) VALUES ('LSO', 'Lesotho ');
        INSERT INTO cc_country (isocode, name) VALUES ('LBR', 'Liberia ');
        INSERT INTO cc_country (isocode, name) VALUES ('LBY', 'Libyan Arab Jamahiriya ');
        INSERT INTO cc_country (isocode, name) VALUES ('LIE', 'Liechtenstein ');
        INSERT INTO cc_country (isocode, name) VALUES ('LTU', 'Lithuania ');
        INSERT INTO cc_country (isocode, name) VALUES ('LUX', 'Luxembourg ');
        INSERT INTO cc_country (isocode, name) VALUES ('MDG', 'Madagascar ');
        INSERT INTO cc_country (isocode, name) VALUES ('MWI', 'Malawi ');
        INSERT INTO cc_country (isocode, name) VALUES ('MYS', 'Malaysia ');
        INSERT INTO cc_country (isocode, name) VALUES ('MDV', 'Maldives ');
        INSERT INTO cc_country (isocode, name) VALUES ('MLI', 'Mali ');
        INSERT INTO cc_country (isocode, name) VALUES ('MLT', 'Malta ');
        INSERT INTO cc_country (isocode, name) VALUES ('MHL', 'Marshall Islands ');
        INSERT INTO cc_country (isocode, name) VALUES ('MTQ', 'Martinique ');
        INSERT INTO cc_country (isocode, name) VALUES ('MRT', 'Mauritania ');
        INSERT INTO cc_country (isocode, name) VALUES ('MUS', 'Mauritius ');
        INSERT INTO cc_country (isocode, name) VALUES ('MYT', 'Mayotte');
        INSERT INTO cc_country (isocode, name) VALUES ('MEX', 'Mexico ');
        INSERT INTO cc_country (isocode, name) VALUES ('FSM', 'Micronesia (Federated States of)');
        INSERT INTO cc_country (isocode, name) VALUES ('MCO', 'Monaco ');
        INSERT INTO cc_country (isocode, name) VALUES ('MNG', 'Mongolia ');
        INSERT INTO cc_country (isocode, name) VALUES ('MNE', 'Montenegro');
        INSERT INTO cc_country (isocode, name) VALUES ('MSR', 'Montserrat ');
        INSERT INTO cc_country (isocode, name) VALUES ('MAR', 'Morocco ');
        INSERT INTO cc_country (isocode, name) VALUES ('MOZ', 'Mozambique ');
        INSERT INTO cc_country (isocode, name) VALUES ('MMR', 'Myanmar ');
        INSERT INTO cc_country (isocode, name) VALUES ('NAM', 'Namibia ');
        INSERT INTO cc_country (isocode, name) VALUES ('NRU', 'Nauru ');
        INSERT INTO cc_country (isocode, name) VALUES ('NPL', 'Nepal ');
        INSERT INTO cc_country (isocode, name) VALUES ('NLD', 'Netherlands ');
        INSERT INTO cc_country (isocode, name) VALUES ('NCL', 'New Caledonia ');
        INSERT INTO cc_country (isocode, name) VALUES ('NZL', 'New Zealand ');
        INSERT INTO cc_country (isocode, name) VALUES ('NIC', 'Nicaragua ');
        INSERT INTO cc_country (isocode, name) VALUES ('NER', 'Niger ');
        INSERT INTO cc_country (isocode, name) VALUES ('NGA', 'Nigeria ');
        INSERT INTO cc_country (isocode, name) VALUES ('NIU', 'Niue ');
        INSERT INTO cc_country (isocode, name) VALUES ('NFK', 'Norfolk Island ');
        INSERT INTO cc_country (isocode, name) VALUES ('MNP', 'Northern Mariana Islands ');
        INSERT INTO cc_country (isocode, name) VALUES ('NOR', 'Norway ');
        INSERT INTO cc_country (isocode, name) VALUES ('PSE', 'Occupied Palestinian Territory ');
        INSERT INTO cc_country (isocode, name) VALUES ('OMN', 'Oman ');
        INSERT INTO cc_country (isocode, name) VALUES ('PAK', 'Pakistan ');
        INSERT INTO cc_country (isocode, name) VALUES ('PLW', 'Palau ');
        INSERT INTO cc_country (isocode, name) VALUES ('PAN', 'Panama ');
        INSERT INTO cc_country (isocode, name) VALUES ('PNG', 'Papua New Guinea ');
        INSERT INTO cc_country (isocode, name) VALUES ('PRY', 'Paraguay ');
        INSERT INTO cc_country (isocode, name) VALUES ('PER', 'Peru ');
        INSERT INTO cc_country (isocode, name) VALUES ('PHL', 'Philippines ');
        INSERT INTO cc_country (isocode, name) VALUES ('PCN', 'Pitcairn ');
        INSERT INTO cc_country (isocode, name) VALUES ('POL', 'Poland ');
        INSERT INTO cc_country (isocode, name) VALUES ('PRT', 'Portugal ');
        INSERT INTO cc_country (isocode, name) VALUES ('PRI', 'Puerto Rico ');
        INSERT INTO cc_country (isocode, name) VALUES ('QAT', 'Qatar ');
        INSERT INTO cc_country (isocode, name) VALUES ('KOR', 'Republic of Korea ');
        INSERT INTO cc_country (isocode, name) VALUES ('MDA', 'Republic of Moldova');
        INSERT INTO cc_country (isocode, name) VALUES ('REU', 'Réunion ');
        INSERT INTO cc_country (isocode, name) VALUES ('ROU', 'Romania ');
        INSERT INTO cc_country (isocode, name) VALUES ('RUS', 'Russian Federation ');
        INSERT INTO cc_country (isocode, name) VALUES ('RWA', 'Rwanda ');
        INSERT INTO cc_country (isocode, name) VALUES ('BLM', 'Saint-Barthélemy');
        INSERT INTO cc_country (isocode, name) VALUES ('SHN', 'Saint Helena ');
        INSERT INTO cc_country (isocode, name) VALUES ('KNA', 'Saint Kitts and Nevis ');
        INSERT INTO cc_country (isocode, name) VALUES ('LCA', 'Saint Lucia ');
        INSERT INTO cc_country (isocode, name) VALUES ('MAF', 'Saint-Martin (French part)');
        INSERT INTO cc_country (isocode, name) VALUES ('SPM', 'Saint Pierre and Miquelon ');
        INSERT INTO cc_country (isocode, name) VALUES ('VCT', 'Saint Vincent and the Grenadines ');
        INSERT INTO cc_country (isocode, name) VALUES ('WSM', 'Samoa ');
        INSERT INTO cc_country (isocode, name) VALUES ('SMR', 'San Marino ');
        INSERT INTO cc_country (isocode, name) VALUES ('STP', 'Sao Tome and Principe ');
        INSERT INTO cc_country (isocode, name) VALUES ('SAU', 'Saudi Arabia ');
        INSERT INTO cc_country (isocode, name) VALUES ('SEN', 'Senegal ');
        INSERT INTO cc_country (isocode, name) VALUES ('SRB', 'Serbia ');
        INSERT INTO cc_country (isocode, name) VALUES ('SYC', 'Seychelles ');
        INSERT INTO cc_country (isocode, name) VALUES ('SLE', 'Sierra Leone ');
        INSERT INTO cc_country (isocode, name) VALUES ('SGP', 'Singapore ');
        INSERT INTO cc_country (isocode, name) VALUES ('SXM', 'Sint Maarten (Dutch part)');
        INSERT INTO cc_country (isocode, name) VALUES ('SVK', 'Slovakia ');
        INSERT INTO cc_country (isocode, name) VALUES ('SVN', 'Slovenia ');
        INSERT INTO cc_country (isocode, name) VALUES ('SLB', 'Solomon Islands ');
        INSERT INTO cc_country (isocode, name) VALUES ('SOM', 'Somalia ');
        INSERT INTO cc_country (isocode, name) VALUES ('ZAF', 'South Africa ');
        INSERT INTO cc_country (isocode, name) VALUES ('ESP', 'Spain ');
        INSERT INTO cc_country (isocode, name) VALUES ('LKA', 'Sri Lanka ');
        INSERT INTO cc_country (isocode, name) VALUES ('SDN', 'Sudan ');
        INSERT INTO cc_country (isocode, name) VALUES ('SUR', 'Suriname ');
        INSERT INTO cc_country (isocode, name) VALUES ('SJM', 'Svalbard and Jan Mayen Islands ');
        INSERT INTO cc_country (isocode, name) VALUES ('SWZ', 'Swaziland ');
        INSERT INTO cc_country (isocode, name) VALUES ('SWE', 'Sweden ');
        INSERT INTO cc_country (isocode, name) VALUES ('CHE', 'Switzerland ');
        INSERT INTO cc_country (isocode, name) VALUES ('SYR', 'Syrian Arab Republic ');
        INSERT INTO cc_country (isocode, name) VALUES ('TJK', 'Tajikistan ');
        INSERT INTO cc_country (isocode, name) VALUES ('THA', 'Thailand ');
        INSERT INTO cc_country (isocode, name) VALUES ('MKD', 'The former Yugoslav Republic of Macedonia ');
        INSERT INTO cc_country (isocode, name) VALUES ('TLS', 'Timor-Leste');
        INSERT INTO cc_country (isocode, name) VALUES ('TGO', 'Togo ');
        INSERT INTO cc_country (isocode, name) VALUES ('TKL', 'Tokelau ');
        INSERT INTO cc_country (isocode, name) VALUES ('TON', 'Tonga ');
        INSERT INTO cc_country (isocode, name) VALUES ('TTO', 'Trinidad and Tobago ');
        INSERT INTO cc_country (isocode, name) VALUES ('TUN', 'Tunisia ');
        INSERT INTO cc_country (isocode, name) VALUES ('TUR', 'Turkey ');
        INSERT INTO cc_country (isocode, name) VALUES ('TKM', 'Turkmenistan ');
        INSERT INTO cc_country (isocode, name) VALUES ('TCA', 'Turks and Caicos Islands ');
        INSERT INTO cc_country (isocode, name) VALUES ('TUV', 'Tuvalu ');
        INSERT INTO cc_country (isocode, name) VALUES ('UGA', 'Uganda ');
        INSERT INTO cc_country (isocode, name) VALUES ('UKR', 'Ukraine ');
        INSERT INTO cc_country (isocode, name) VALUES ('ARE', 'United Arab Emirates ');
        INSERT INTO cc_country (isocode, name) VALUES ('GBR', 'United Kingdom of Great Britain and Northern Ireland');
        INSERT INTO cc_country (isocode, name) VALUES ('TZA', 'United Republic of Tanzania ');
        INSERT INTO cc_country (isocode, name) VALUES ('USA', 'United States of America');
        INSERT INTO cc_country (isocode, name) VALUES ('VIR', 'United States Virgin Islands ');
        INSERT INTO cc_country (isocode, name) VALUES ('URY', 'Uruguay ');
        INSERT INTO cc_country (isocode, name) VALUES ('UZB', 'Uzbekistan ');
        INSERT INTO cc_country (isocode, name) VALUES ('VUT', 'Vanuatu ');
        INSERT INTO cc_country (isocode, name) VALUES ('VEN', 'Venezuela (Bolivarian Republic of)');
        INSERT INTO cc_country (isocode, name) VALUES ('VNM', 'Viet Nam ');
        INSERT INTO cc_country (isocode, name) VALUES ('WLF', 'Wallis and Futuna Islands ');
        INSERT INTO cc_country (isocode, name) VALUES ('ESH', 'Western Sahara ');
        INSERT INTO cc_country (isocode, name) VALUES ('YEM', 'Yemen ');
        INSERT INTO cc_country (isocode, name) VALUES ('ZMB', 'Zambia ');
        INSERT INTO cc_country (isocode, name) VALUES ('ZWE', 'Zimbabwe ');";

        echo "* Inserting data into country table".PHP_EOL;
        Airtime190Upgrade::execSqlQuery($sql);
    }
}

class AirtimeIni{

    const CONF_FILE_AIRTIME = "/etc/airtime/airtime.conf";
    const CONF_FILE_PYPO = "/etc/airtime/pypo.cfg";
    const CONF_FILE_RECORDER = "/etc/airtime/recorder.cfg";
    const CONF_FILE_LIQUIDSOAP = "/etc/airtime/liquidsoap.cfg";
    const CONF_FILE_MEDIAMONITOR = "/etc/airtime/media-monitor.cfg";
    const CONF_FILE_API_CLIENT = "/etc/airtime/api_client.cfg";
    const CONF_FILE_MONIT = "/etc/monit/conf.d/airtime-monit.cfg";

    /**
     * This function updates an INI style config file.
     *
     * A property and the value the property should be changed to are
     * supplied. If the property is not found, then no changes are made.
     *
     * @param string $p_filename
     *      The path the to the file.
     * @param string $p_property
     *      The property to look for in order to change its value.
     * @param string $p_value
     *      The value the property should be changed to.
     *
     */
    public static function UpdateIniValue($p_filename, $p_property, $p_value)
    {
        $lines = file($p_filename);
        $n=count($lines);
        foreach ($lines as &$line) {
            if ($line[0] != "#"){
                $key_value = explode("=", $line);
                $key = trim($key_value[0]);

                if ($key == $p_property){
                    $line = "$p_property = $p_value".PHP_EOL;
                }
            }
        }

        $fp=fopen($p_filename, 'w');
        for($i=0; $i<$n; $i++){
            fwrite($fp, $lines[$i]);
        }
        fclose($fp);
    }

    public static function CreateMonitFile(){
        if (!copy(__DIR__."/../../../python_apps/monit/airtime-monit.cfg", AirtimeIni::CONF_FILE_MONIT)){
            echo "Could not copy airtime-monit.cfg to /etc/monit/conf.d/. Exiting.";
            exit(1);
        }
    }

    public static function ReadPythonConfig($p_filename)
    {
        $values = array();

        $lines = file($p_filename);
        $n=count($lines);
        for ($i=0; $i<$n; $i++) {
            if (strlen($lines[$i]) && !in_array(substr($lines[$i], 0, 1), array('#', PHP_EOL))){
                 $info = explode("=", $lines[$i]);
                 $values[trim($info[0])] = trim($info[1]);
             }
        }

        return $values;
    }

    public static function MergeConfigFiles($configFiles, $suffix) {
        foreach ($configFiles as $conf) {
            if (file_exists("$conf$suffix.bak")) {

                if($conf === AirtimeIni::CONF_FILE_AIRTIME) {
                    // Parse with sections
                    $newSettings = parse_ini_file($conf, true);
                    $oldSettings = parse_ini_file("$conf$suffix.bak", true);
                }
                else {
                    $newSettings = AirtimeIni::ReadPythonConfig($conf);
                    $oldSettings = AirtimeIni::ReadPythonConfig("$conf$suffix.bak");
                }

                $settings = array_keys($newSettings);

                foreach($settings as $section) {
                    if(isset($oldSettings[$section])) {
                        if(is_array($oldSettings[$section])) {
                            $sectionKeys = array_keys($newSettings[$section]);
                            foreach($sectionKeys as $sectionKey) {
                                if(isset($oldSettings[$section][$sectionKey])) {
                                    AirtimeIni::UpdateIniValue($conf, $sectionKey, $oldSettings[$section][$sectionKey]);
                                }
                            }
                        }
                        else {
                            AirtimeIni::UpdateIniValue($conf, $section, $oldSettings[$section]);
                        }
                    }
                }
            }
        }
    }

    public static function upgradeConfigFiles(){

        $configFiles = array(AirtimeIni::CONF_FILE_AIRTIME,
                             AirtimeIni::CONF_FILE_PYPO,
                             AirtimeIni::CONF_FILE_RECORDER,
                             AirtimeIni::CONF_FILE_LIQUIDSOAP);

        // Backup the config files
        $suffix = date("Ymdhis")."-1.9.0";
        foreach ($configFiles as $conf) {
            if (file_exists($conf)) {
                echo "Backing up $conf to $conf$suffix.bak".PHP_EOL;
                copy($conf, $conf.$suffix.".bak");
            }
        }
        AirtimeIni::CreateIniFiles();
        AirtimeIni::MergeConfigFiles($configFiles, $suffix);
    }

    /**
     * This function creates the /etc/airtime configuration folder
     * and copies the default config files to it.
     */
    public static function CreateIniFiles()
    {
        if (!file_exists("/etc/airtime/")){
            if (!mkdir("/etc/airtime/", 0755, true)){
                echo "Could not create /etc/airtime/ directory. Exiting.";
                exit(1);
            }
        }

        $AIRTIME_SRC = realpath(__DIR__.'/../../../airtime_mvc');
        $AIRTIME_PYTHON_APPS = realpath(__DIR__.'/../../../python_apps');

        if (!copy($AIRTIME_SRC."/build/airtime.conf", AirtimeIni::CONF_FILE_AIRTIME)){
            echo "Could not copy airtime.conf to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy($AIRTIME_PYTHON_APPS."/pypo/pypo.cfg", AirtimeIni::CONF_FILE_PYPO)){
            echo "Could not copy pypo.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy($AIRTIME_PYTHON_APPS."/show-recorder/recorder.cfg", AirtimeIni::CONF_FILE_RECORDER)){
            echo "Could not copy recorder.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
        if (!copy($AIRTIME_PYTHON_APPS."/pypo/liquidsoap_scripts/liquidsoap.cfg", AirtimeIni::CONF_FILE_LIQUIDSOAP)){
            echo "Could not copy liquidsoap.cfg to /etc/airtime/. Exiting.";
            exit(1);
        }
    }
}

class Airtime190Upgrade{

    public static function InstallAirtimePhpServerCode($phpDir)
    {

        $AIRTIME_SRC = realpath(__DIR__.'/../../../airtime_mvc');

        echo "* Installing PHP code to ".$phpDir.PHP_EOL;
        exec("mkdir -p ".$phpDir);
        exec("cp -R ".$AIRTIME_SRC."/* ".$phpDir);
    }

    public static function CopyUtils()
    {
        $utilsSrc = __DIR__."/../../../utils";

        echo "* Installing binaries to ".CONF_DIR_BINARIES.PHP_EOL;
        exec("mkdir -p ".CONF_DIR_BINARIES);
        exec("cp -R ".$utilsSrc." ".CONF_DIR_BINARIES);
    }

    /* Removes pypo, media-monitor, show-recorder and utils from system. These will
     * be reinstalled by the main airtime-upgrade script.
     */
    public static function UninstallBinaries()
    {
        echo "* Removing Airtime binaries from ".CONF_DIR_BINARIES.PHP_EOL;
        $command = "rm -rf $(ls -d /usr/lib/airtime/* | grep -v airtime_virtualenv)";
        exec($command);
    }


    public static function removeOldAirtimeImport(){
        exec('rm -f "/usr/bin/airtime-import"');
    }

    public static function updateAirtimeImportSymLink(){
        $dir = "/usr/lib/airtime/utils/airtime-import/airtime-import";
        exec("ln -s $dir /usr/bin/airtime-import");
    }

    public static function execSqlQuery($sql){
        global $CC_DBC;

        $result = $CC_DBC->query($sql);
        if (PEAR::isError($result)) {
            echo "* Failed sql query: $sql".PHP_EOL;
            echo "* Message {$result->getMessage()}".PHP_EOL;
        }

        return $result;
    }

    public static function connectToDatabase(){
        global $CC_DBC, $CC_CONFIG;

        $values = parse_ini_file('/etc/airtime/airtime.conf', true);

        // Database config
        $CC_CONFIG['dsn']['username'] = $values['database']['dbuser'];
        $CC_CONFIG['dsn']['password'] = $values['database']['dbpass'];
        $CC_CONFIG['dsn']['hostspec'] = $values['database']['host'];
        $CC_CONFIG['dsn']['phptype'] = 'pgsql';
        $CC_CONFIG['dsn']['database'] = $values['database']['dbname'];

        $CC_DBC = DB::connect($CC_CONFIG['dsn'], FALSE);
    }

    public static function backupFileInfoInStorToFile($values) {

        echo "Save DbMd to File".PHP_EOL;

        $stor_dir = realpath($values['general']['base_files_dir']."/stor");

        $files = CcFilesQuery::create()
           ->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
           ->find();

        $dumpFile = __DIR__"/storDump.txt";
        $fh = fopen($dumpFile, 'w') or die("can't open file to backup stor.");

        $s = "SF_BACKUP";

        foreach ($files as $file) {

            $filepath = $file->getDbFilepath();

            if (substr($filepath, 0, strlen($stor_dir)) == $stor_dir) {

                $recorded_show = CcShowInstancesQuery::create()
                    ->filterByDbRecordedFile($file->getDbId())
                    ->findOne();

                if (isset($recorded_show)) {

                    $start_time = $recorded_show->getDbStarts();
                    $title = $file->getDbTrackTitle();

                    $start_time = str_replace(" ", "-", $start_time);
                    $start_time = str_replace(":", "-", $start_time);

                    //$start_time like yyyy-mm-dd-hh-MM-ss
                    list($yyyy, $mm, $dd, $hh, $MM, $ss) = explode("-", $start_time);

                    $data = "1$s$filepath$s$title$s$yyyy$s$mm$s$dd$s$hh$s$MM\n";
                }
                else {

                    $artist = $file->getDbArtistName();
                    $album = $file->getDbAlbumTitle();
                    $track = $file->getDbTrackNumber();
                    $title = $file->getDbTrackTitle();

                    $data = "0$s$filepath$s$title$s$artist$s$album$s$track\n";
                }

                fwrite($fh, $data);
            }
        }

        fclose($fh);
    }

    /* Old database had a "fullpath" column that stored the absolute path of each track. We have to
     * change it so that the "fullpath" column has path relative to the "directory" column.
     */
    public static function installMediaMonitor($values){

        $propel_stor_dir = CcMusicDirsQuery::create()
           ->filterByType('stor')
           ->findOne();

        $propel_link_dir = CcMusicDirsQuery::create()
           ->filterByType('link')
           ->findOne();

        /* Handle Database Changes. */
        $stor_dir = realpath($values['general']['base_files_dir']."/stor")."/";
        echo "* Inserting stor directory location $stor_dir into music_dirs table".PHP_EOL;
        $propel_stor_dir->setDirectory($stor_dir);
        $propel_stor_dir->save();

        echo "Creating media-monitor log file".PHP_EOL;
        mkdir("/var/log/airtime/media-monitor/", 755, true);
        touch("/var/log/airtime/media-monitor/media-monitor.log");

        /* create media monitor config: */
        if (!copy(__DIR__."/../../../python_apps/media-monitor/media-monitor.cfg", AirtimeIni::CONF_FILE_MEDIAMONITOR)){
            echo "Could not copy media-monitor.cfg to /etc/airtime/. Exiting.";
        }
        if (!copy(__DIR__."/../../../python_apps/api_clients/api_client.cfg", AirtimeIni::CONF_FILE_API_CLIENT)){
            echo "Could not copy api_client.cfg to /etc/airtime/. Exiting.";
        }

        AirtimeIni::UpdateIniValue(AirtimeIni::CONF_FILE_API_CLIENT, "api_key", $values["general"]["api_key"]);

        echo "Reorganizing files in stor directory".PHP_EOL;

        $cwd = __DIR__;
        $mediaMonitorUpgradePath = __DIR__."/media-monitor-upgrade.py";
        $command = "cd $cwd && su -c \"python $mediaMonitorUpgradePath\"";
        exec($command, $output);
        print_r($output);

        if (isset($output[0])) {

            $oldAndNewFileNames = json_decode($output[0]);

            $stor_dir_id = $propel_stor_dir->getId();
            foreach ($oldAndNewFileNames as $pair){
                $relPathNew = pg_escape_string(substr($pair[1], strlen($stor_dir)));
                $absPathOld = pg_escape_string($pair[0]);
                $sql = "UPDATE cc_files SET filepath = '$relPathNew', directory=$stor_dir_id WHERE filepath = '$absPathOld'";
                echo $sql.PHP_EOL;
                Airtime190Upgrade::execSqlQuery($sql);
            }
        }

        echo "Upgrading Linked Files".PHP_EOL;

        //HANDLE LINKED FILES HERE.

        $db_files = CcFilesQuery::create()
           ->setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
           ->filterByDbDirectory(NULL)
           ->find();

        //Check to see if the file still exists. (Could have still some entries under the stor dir or linked files that don't exist)
        $link_dir_id = $propel_link_dir->getId();
        foreach($db_files as $db_file) {
            $filepath = $db_file->getDbFilepath();
            echo $filepath.PHP_EOL;

            if (!file_exists($filepath)) {
                $db_file->delete();
                echo "Removed Missing File: ".$filepath.PHP_EOL;
            }
            else {
                $db_file->setDbDirectory($link_dir_id);
                $db_file->save();
            }
        }
    }
}




AirtimeInstall::CreateZendPhpLogFile();

/* In version 1.9.0 we have have switched from daemontools to more traditional
 * init.d daemon system. Let's remove all the daemontools files
 */
exec("/usr/bin/airtime-pypo-stop");
exec("/usr/bin/airtime-show-recorder-stop");

exec("svc -d /etc/service/pypo");
exec("svc -d /etc/service/pypo/log");
exec("svc -d /etc/service/pypo-liquidsoap");
exec("svc -d /etc/service/pypo-liquidsoap/log");
exec("svc -d /etc/service/recorder");
exec("svc -d /etc/service/recorder/log");

$pathnames = array("/usr/bin/airtime-pypo-start",
                "/usr/bin/airtime-pypo-stop",
                "/usr/bin/airtime-show-recorder-start",
                "/usr/bin/airtime-show-recorder-stop",
                "/usr/bin/airtime-media-monitor-start",
                "/usr/bin/airtime-media-monitor-stop",
                "/etc/service/pypo",
                "/etc/service/pypo-liquidsoap",
                "/etc/service/media-monitor",
                "/etc/service/recorder",
                "/var/log/airtime/pypo/main",
                "/var/log/airtime/pypo-liquidsoap/main",
                "/var/log/airtime/show-recorder/main"
                );

foreach ($pathnames as $pn){
    echo "Removing $pn\n";
    exec("rm -rf \"$pn\"");
}


/* update Airtime Server PHP files */
$values = parse_ini_file(AirtimeIni::CONF_FILE_AIRTIME, true);
$phpDir = $values['general']['airtime_dir'];
Airtime190Upgrade::InstallAirtimePhpServerCode($phpDir);

/* update utils (/usr/lib/airtime) folder */
Airtime190Upgrade::UninstallBinaries();
Airtime190Upgrade::CopyUtils();

/* James made a new airtime-import script, lets remove the old airtime-import php script,
 *install the new airtime-import.py script and update the /usr/bin/symlink.
 */
Airtime190Upgrade::removeOldAirtimeImport();
Airtime190Upgrade::updateAirtimeImportSymLink();

Airtime190Upgrade::connectToDatabase();

if(AirtimeInstall::DbTableExists('doctrine_migration_versions') === false) {
    $migrations = array('20110312121200', '20110331111708', '20110402164819', '20110406182005');
    foreach($migrations as $migration) {
        AirtimeInstall::BypassMigrations(__DIR__, $migration);
    }
}
/* adding music_dir and country table. 20110629143017 and 20110713161043 respetivly */
AirtimeInstall::MigrateTablesToVersion(__DIR__, '20110713161043');

AirtimeInstall::InsertCountryDataIntoDatabase();

AirtimeIni::CreateMonitFile();

AirtimeInstall::CreateSymlinksToUtils();

/* create cron file for phone home stat */
AirtimeInstall::CreateCronFile();

Airtime190Upgrade::backupFileInfoInStorToFile($values);
Airtime190Upgrade::installMediaMonitor($values);

AirtimeIni::upgradeConfigFiles();




