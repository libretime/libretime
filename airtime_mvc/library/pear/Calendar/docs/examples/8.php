<?php
/**
 * Description: client for the SOAP Calendar Server
 */
if ( version_compare(phpversion(), "5.0.0", ">") ) {
    die('PHP 5 has problems with PEAR::SOAP Client (8.0RC3)
        - remove @ before include below to see why');
}

if (!@include('SOAP'.DIRECTORY_SEPARATOR.'Client.php')) {
    die('You must have PEAR::SOAP installed');
}

// Just to save manaul modification...
$basePath = explode('/', $_SERVER['SCRIPT_NAME']);
array_pop($basePath);
$basePath = implode('/', $basePath);
$url = 'http://'.$_SERVER['SERVER_NAME'].$basePath.'/7.php?wsdl';

if (!isset($_GET['y'])) $_GET['y'] = date('Y');
if (!isset($_GET['m'])) $_GET['m'] = date('n');

$wsdl = new SOAP_WSDL ($url);

echo ( '<pre>'.$wsdl->generateProxyCode().'</pre>' );

$calendarClient = $wsdl->getProxy();

$month = $calendarClient->getMonth((int)$_GET['y'],(int)$_GET['m']);

if ( PEAR::isError($month) ) {
    die ( $month->toString() );
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title> Calendar over the Wire </title>
</head>
<body>
<h1>Calendar Over the Wire (featuring PEAR::SOAP)</h1>
<table>
<caption><b><?php echo ( $month->monthname );?></b></caption>
<tr>
<th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th><th>S</th>
</tr>
<?php
foreach ( $month->days as $day ) {

    if ( $day->isFirst === 1 )
        echo ( "<tr>\n" );
    if ( $day->isEmpty === 1 ) {
        echo ( "<td></td>" );
    } else {
        echo ( "<td>".$day->day."</td>" );
    }
    if ( $day->isLast === 1 )
        echo ( "</tr>\n" );
}
?>
<tr>
</table>
<p>Enter Year and Month to View:</p>
<form action="<?php echo ( $_SERVER['PHP_SELF'] ); ?>" method="get">
Year: <input type="text" size="4" name="y" value="<?php echo ( $_GET['y'] ); ?>">&nbsp;
Month: <input type="text" size="2" name="m" value="<?php echo ( $_GET['m'] ); ?>">&nbsp;
<input type="submit" value="Fetch Calendar">
</form>
</body>
</html>