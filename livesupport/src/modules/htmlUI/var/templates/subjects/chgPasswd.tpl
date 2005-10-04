{assign var="dynform" value=$SUBJECTS->getChgPasswdForm($SUBJECTS->Base->gb->getSubjName($_REQUEST.id), false)}

<div class="container_elements" style="width: 607px;">
     <h1>##Change Password for "{$SUBJECTS->Base->gb->getSubjName($_REQUEST.id)}"##</h1>
    {include file="sub/dynForm_plain.tpl"}
</div>
