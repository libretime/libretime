<?php
/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/
include("xmlrpc.inc");

$host       = "localhost";
$port           = 80;
$serverscript   = dirname($_SERVER['PHP_SELF'])."/alib_xr.php";
$log            = '';
$ak             = $_REQUEST['ak'];
$sessid         = $_REQUEST['sessid'];

switch($ak){
    case"test":
        $f=new xmlrpcmsg('alib.xrTest',
            array(new xmlrpcval($_REQUEST['str'], "string"),
                new xmlrpcval($_REQUEST['sessid'], "string")));
    break;
    case"login":
        $f=new xmlrpcmsg('alib.login',array(
            new xmlrpcval($_REQUEST['login'], "string"),
            new xmlrpcval($_REQUEST['pass'], "string")
        ));
    break;
    case"logout":
        $f=new xmlrpcmsg('alib.logout', array(
            new xmlrpcval($_REQUEST['sessid'], "string")
        ));
    break;
}

switch($ak){
    case"test":
    case"login":
    case"logout":
        $c=new xmlrpc_client($serverscript, $host, $port);
            #$c->setDebug(1);
            $r=$c->send($f);
        if (!($r->faultCode()>0)) {
            $v=$r->value();
            $log = $v->serialize();
            if($ak=='test')
            {
                $log = split('_',$log);
                $log="{$log[0]}\nusername: {$log[1]}\ntoken: {$log[2]}";
            }
            if($ak=='login') $sessid = $v->scalarval();
            if($ak=='logout') $sessid = '';
        } else {
        	$log = "Fault:\n Code: ".$r->faultCode()."\n".
        	    "Reason:'".$r->faultString()."'<BR>\n";
        }
    break;
}
    
?>
<html><head>
<title>Alib XMLRPC test client</title>
</head><body>
<h2>Alib XMLRPC test client</h2>
XMLRPC server: <b><?="http://$host:$port$serverscript"?></b><br>

<?=($log?'<h3>Output:</h3>':'')?>
<pre style="background-color:#ddd"><?=$log?></pre>

<hr>
<h3>test</h3>
flip teststring to uppercase and print username and session token<br>
<form method="post">
test string: <input type="text" name="str" value="abCDef"><br>
token: <input type="text" name="sessid" value="<?=$sessid?>" size="34"><br>
<input type="hidden" name="ak" value="test">
<input type="submit" value="Test">
</form>
<hr>
<h3>login</h3>
<form method="post">
username: <input type="text" name="login" value="test1"><br>
password: <input type="password" name="pass" value="a"><br>
<input type="hidden" name="ak" value="login">
<input type="submit" value="Login">
</form>
<hr>
<h3>logout</h3>
<form method="post">
token: <input type="text" name="sessid" value="<?=$sessid?>" size="34"><br>
<input type="hidden" name="ak" value="logout">
<input type="submit" value="Logout">
</form>
<hr>
<a href="../">Back</a>
</body></html>
