<?xml version="1.0"?> 
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
<html>
<head>
    <title>LiveSupport unit test results</title>
</head>
<body>
<h1>Preface</h1>
This document is part of the
<a href="http://livesupport.campware.org/">LiveSupport</a>
project, Copyright &#169; 2004 <a href="http://www.mdlf.org/">Media
Development Loan Fund</a>, under the GNU
<a href="http://www.gnu.org/licenses/gpl.html">GPL</a>.
<br/>
This is an automatically generated document.
<h1>Scope</h1>
This document contains the generated unit test results for the
<a href="http://livesupport.campware.org/">LiveSupport</a> project.
<h1>Summary</h1>
<table>
    <tr>
        <td><b>Total number of tests:</b></td>
        <td><xsl:value-of select="count(//Test | //FailedTest)"/></td>
    </tr>
    <tr>
        <td><b>Tests passed:</b></td>
        <td><xsl:value-of select="count(//SuccessfulTests/Test)"/></td>
    </tr>
    <tr>
        <td><b>Tests failed:</b></td>
        <td><xsl:value-of select="count(//FailedTests/FailedTest)"/></td>
    </tr>
</table>
<h1>Tests</h1>
<table>
    <tr>
        <th>test name</th>
        <th>test status</th>
    </tr>
<xsl:for-each select="//Test | //FailedTest">
    <xsl:sort select="Name"/>
    <tr>
        <td><xsl:value-of select="Name"/></td>
        <xsl:if test="ancestor::FailedTests"><td bgcolor="red">failed</td></xsl:if>
        <xsl:if test="ancestor::SuccessfulTests"><td bgcolor="lightblue">passed</td></xsl:if>
    </tr>
</xsl:for-each>
</table>
</body>
</html>
</xsl:template>

</xsl:stylesheet>

