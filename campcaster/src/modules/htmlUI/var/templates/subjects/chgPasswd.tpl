{assign var="dynform" value=$SUBJECTS->getChgPasswdForm(Subjects::GetSubjName($_REQUEST.id), false)}

<div class="container_elements" style="width: 607px;">
     <h1>{tra str='Change password for: $1' 1=Subjects::GetSubjName($_REQUEST.id)}</h1>
    {include file="sub/dynForm_plain.tpl"}
</div>
