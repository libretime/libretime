{assign var="subjectName" value=$SUBJECTS->getSubjectName($_REQUEST.id)}
{assign var="dynform" value=$SUBJECTS->getChgPasswdForm($subjectName)}

<div class="container_elements" style="width: 607px;">
     <h1>{tra str='Change password for: $1' 1=$subjectName}</h1>
    {include file="sub/dynForm_plain.tpl"}
</div>
