<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<!--  $Id: smarty-static.tpl,v 1.3 2004/10/15 20:30:56 ths Exp $ -->
<html>
<head>
    <title>Smarty template for ArraySmarty renderer: 2 column layout example</title>
    <style type="text/css">
        {literal}
       .errors {
       font-family: sans-serif;
       color : #000;
       background-color : #FFF;
       font-size : 12pt;
       }
       .label {
       font-family: sans-serif;
       color : Navy;
       font-size : 11px;
       text-align : right;
       vertical-align : top;
       white-space: nowrap;
       }
       .element {
       font-family: sans-serif;
       background-color : #EEE;
       text-align : left;
       white-space: nowrap;
       }
       .note {
       font-family: sans-serif;
       background-color : #EEE;
       text-align : center;
       font-size : 10pt;
       color : AAA;
       white-space: nowrap;
       }
       th {
       font-family: sans-serif;
       font-size : small;
       color : #FFF;
       background-color : #AAA;
       }
       .maintable {
       border : thin dashed #D0D0D0;
       background-color : #EEE;
       }
       {/literal}
    </style>
{$form.javascript}
</head>

<body>

<form {$form.attributes}>
{$form.hidden}

<table class="maintable" width="600" align="center">
    <tr>
        <td width="50%" valign="top"><!-- Personal info -->
            <table width="100%" cellpadding="4">
                <tr><th colspan="2">{$form.header.personal}</th></tr>
                <tr>
                    <td class="label">{$form.name.label}</td>
                    <td class="element">{$form.name.error}
                        <table cellspacing="0" cellpadding="1">
                            <tr>
                                <td>{$form.name.first.html}</td>
                                <td>{$form.name.last.html}</td>
                            </tr>
                            <tr>
                                <td><font size="1" color="grey">{$form.name.first.label}</font></td>
                                <td><font size="1" color="grey">{$form.name.last.label}</font></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="label">{$form.phone.label}</td>
                    <td class="element">{$form.phone.html}</td>
                </tr>
                <tr>
                    <td class="label">{$form.email.label}</td>
                    <td class="element">{$form.email.html}</td>
                </tr>
                <tr><td colspan="2" class="note">{$form.pass.label_note}</td></tr>
                <tr>
                    <td class="label">{$form.pass.label}</td>
                    <td class="element">{$form.pass.html}</td>
                </tr>
            </table>
        </td>

        <td width="50%" valign="top"><!-- Company info -->
            <table width="100%" cellpadding="4">
                <tr><th colspan="2">{$form.header.company_info}</th></tr>
                <tr>
                    <td class="label">{$form.company.label}</td>
                    <td class="element">{$form.company.html}</td>
                </tr>
                <tr>
                    <td class="label" valign="top">{$form.street.label}</td>
                    <td class="element">{$form.street.html}</td>
                </tr>
                <tr>
                    <td class="label">{$form.address.label}</td>
                    <td class="element">{$form.address.error}
                        <table cellspacing="0" cellpadding="1">
                            <tr>
                                <td>{$form.address.zip.html}</td>
                                <td>{$form.address.city.html}</td>
                            </tr>
                            <tr>
                                <td><font size="1" color="grey">{$form.address.zip.label}</font></td>
                                <td><font size="1" color="grey">{$form.address.city.label}</font></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="label">{$form.country.label}</td>
                    <td class="element">{$form.country.html}</td>
                </tr>
                <tr>
                    <td class="label">{$form.destination.label}</td>
                    <td class="element">{$form.destination.html}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table width="600" align="center">
    <tr>
        <td>{$form.requirednote}</td>
        <td align="right">{$form.reset.html}&nbsp;{$form.submit.html}</td>
    </tr>
    <tr>
        <td colspan="2" style="font-size:11px; color: navy;"><br />{$form.news.html}</td>
    </tr>
</table>

</form>

<br />
<b>Collected Errors:</b><br />
{foreach key=name item=error from=$form.errors}
    <font color="red">{$error}</font> in element [{$name}]<br />
{/foreach}

&nbsp;
<p><strong>The used "Static" Array</strong></p>
<pre style="font-size: 12px;">
{$static_array|htmlentities}
</pre>

</body>
</html>