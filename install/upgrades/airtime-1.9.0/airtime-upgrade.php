<?php
/**
 * @package Airtime
 * @subpackage StorageServer
 * @copyright 2010 Sourcefabric O.P.S.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

set_include_path(__DIR__.'/../../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());
require_once __DIR__.'/../../../airtime_mvc/application/configs/conf.php';
require_once(dirname(__FILE__).'/../../include/AirtimeInstall.php');
require_once(dirname(__FILE__).'/../../include/AirtimeIni.php');

AirtimeInstall::CreateZendPhpLogFile();

const CONF_DIR_BINARIES = "/usr/lib/airtime";
const CONF_FILE_AIRTIME = "/etc/airtime/airtime.conf";
const CONF_FILE_MEDIAMONITOR = "/etc/airtime/media-monitor.cfg";

function BypassMigrations($version)
{
    $appDir = __DIR__."/../../airtime_mvc";
    $dir = __DIR__;
    $command = "php $appDir/library/doctrine/migrations/doctrine-migrations.phar ".
                "--configuration=$dir/../../DoctrineMigrations/migrations.xml ".
                "--db-configuration=$appDir/library/doctrine/migrations/migrations-db.php ".
                "--no-interaction --add migrations:version $version";
    system($command);
}

function MigrateTablesToVersion($version)
{
    $appDir = __DIR__."/../../airtime_mvc";
    $dir = __DIR__;
    $command = "php $appDir/library/doctrine/migrations/doctrine-migrations.phar ".
                "--configuration=$dir/../../DoctrineMigrations/migrations.xml ".
                "--db-configuration=$appDir/library/doctrine/migrations/migrations-db.php ".
                "--no-interaction migrations:migrate $version";
    system($command);
}

function InstallAirtimePhpServerCode($phpDir)
{
    global $CC_CONFIG;

    $AIRTIME_SRC = realpath(__DIR__.'/../../../airtime_mvc');

    echo "* Installing PHP code to ".$phpDir.PHP_EOL;
    exec("mkdir -p ".$phpDir);
    exec("cp -R ".$AIRTIME_SRC."/* ".$phpDir);
}

function CopyUtils()
{
    $utilsSrc = __DIR__."/../../../utils";

    echo "* Installing binaries to ".CONF_DIR_BINARIES.PHP_EOL;
    exec("mkdir -p ".CONF_DIR_BINARIES);
    exec("cp -R ".$utilsSrc." ".CONF_DIR_BINARIES);
}

/* Removes pypo, media-monitor, show-recorder and utils from system. These will
   be reinstalled by the main airtime-upgrade script. */
function UninstallBinaries()
{
    echo "* Removing Airtime binaries from ".CONF_DIR_BINARIES.PHP_EOL;
    exec('rm -rf "'.CONF_DIR_BINARIES.'"');
}


function removeOldAirtimeImport(){
    exec('rm -f "/usr/bin/airtime-import"');
    exec('rm -f "/usr/lib/airtime/utils/airtime-import.php"');
    exec('rm -rf "/usr/lib/airtime/utils/airtime-import"');
}

function updateAirtimeImportSymLink(){
    $dir = "/usr/lib/airtime/utils/airtime-import/airtime-import";
    exec("ln -s $dir /usr/bin/airtime-import");
}

function execSqlQuery($sql){
    global $CC_DBC;

    $result = $CC_DBC->query($sql);
    if (PEAR::isError($result)) {
        echo "* Failed sql query: $sql".PHP_EOL;
        echo "* Message {$result->getMessage()}".PHP_EOL;
    }
    
    return $result;
}

function connectToDatabase(){
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


//update Airtime Server PHP files
$values = parse_ini_file(CONF_FILE_AIRTIME, true);
$phpDir = $values['general']['airtime_dir'];
InstallAirtimePhpServerCode($phpDir);

//update utils (/usr/lib/airtime) folder
UninstallBinaries();
CopyUtils();

//James's made a new airtime-import script, lets remove the old airtime-import php script,
//install the new airtime-import.py script and update the /usr/bin/symlink.
removeOldAirtimeImport();
updateAirtimeImportSymLink();

connectToDatabase();

if(AirtimeInstall::DbTableExists('doctrine_migration_versions') === false) {
    $migrations = array('20110312121200', '20110331111708', '20110402164819', '20110406182005');
    foreach($migrations as $migration) {
        AirtimeInstall::BypassMigrations(__DIR__, $migration);
    }
}
// adding music_dir and country table. 20110629143017 and 20110713161043 respetivly
AirtimeInstall::MigrateTablesToVersion(__DIR__, '20110713161043');

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
execSqlQuery($sql);

//create cron file for phone home stat
AirtimeInstall::CreateCronFile();



//Handle Database Changes.
$stor_dir = realpath($values['general']['base_files_dir']."/stor")."/";
echo "* Inserting stor directory location $stor_dir into music_dirs table".PHP_EOL;
$sql = "UPDATE cc_music_dirs SET directory='$stor_dir' WHERE type='stor'";
echo $sql.PHP_EOL;
execSqlQuery($sql);

$sql = "SELECT id FROM cc_music_dirs WHERE type='stor'";
echo $sql.PHP_EOL;
$rows = execSqlQuery($sql);

//echo "STORAGE ROW ID: $rows[0]";

//old database had a "fullpath" column that stored the absolute path of each track. We have to
//change it so that the "fullpath" column has path relative to the "directory" column.

echo "Creating media-monitor log file";
mkdir("/var/log/airtime/media-monitor/", 755, true);
touch("/var/log/airtime/media-monitor/media-monitor.log");

//create media monitor config:
if (!copy(__DIR__."/../../../python_apps/media-monitor/media-monitor.cfg", CONF_FILE_MEDIAMONITOR)){
    echo "Could not copy media-monitor.cfg to /etc/airtime/. Exiting.";
}

echo "Reorganizing files in stor directory";
$mediaMonitorUpgradePath = realpath(__DIR__."/../../../python_apps/media-monitor/media-monitor-upgrade.py");
exec("su -c \"python $mediaMonitorUpgradePath\"", $output);

print_r($output);

$oldAndNewFileNames = json_decode($output[0]);

foreach ($oldAndNewFileNames as $pair){
    $relPathNew = pg_escape_string(substr($pair[1], strlen($stor_dir)));
    $absPathOld = pg_escape_string($pair[0]);
    $sql = "UPDATE cc_files SET filepath = '$relPathNew', directory=1 WHERE filepath = '$absPathOld'";
    echo $sql.PHP_EOL;
    execSqlQuery($sql);
}



