<?php
function phtml($str) {
    echo htmlspecialchars($str);
}

class Maintenance {
    function listLanguages()
    {
        $mode       = Localizer::GetMode();
        $languages  = Localizer::getAllLanguages($mode);
        ?>
        <table style="background-color: #d5e2ee; border: 1px solid #8baed1; margin-left: 10px; margin-top: 5px;" width="700px;">
        <tr><th colspan="4">Installed Languages:</th></tr>
        <?php
        foreach ($languages as $l) {
            ?>
            <tr><td><?php phtml($l->m_languageId); ?></td><td><?php phtml($l->m_englishName); ?></td><td><?php phtml($l->m_nativeName); ?></td><td><?php phtml('Edit'); ?></td>
            <?php
        }
        ?>
        </table>
        <?php
    }

    function languageForm($validate=FALSE)
    {
        require_once 'HTML/QuickForm.php';
        require_once 'form_function.php';
        $form = new  HTML_QuickForm();
        parseArr2Form($form, addLanguageFormArr());

        if ($validate) {
            if ($form->validate()) {
                return $form->getSubmitValues();
            } else {
                return FALSE;
            }
        }

        ?>
        <table style="background-color: #d5e2ee; border: 1px solid #8baed1; margin-left: 10px; margin-top: 5px;" width="700px;">
        <tr><th>Add language</th></tr>
            <tr><td align="center"><?php echo $form->toHtml(); ?></td><tr>
        </table>
        <?php
    }

    function doAddLanguage()
    {
        if ($values = Maintenance::languageForm(TRUE)) {
            // store from here
            global $g_localizerConfig;
            $className = "LocalizerFileFormat_".strtoupper($g_localizerConfig['DEFAULT_FILE_TYPE']);
            $storage = new $className();

            if ($storage->addLanguage($values)) {
                return TRUE;
            }
        }

        return FALSE;
    }
}

if ($g_localizerConfig['MAINTENANCE']) {
    ?>
    <table>
        <tr><td valign="top"> <!-- Begin top control panel -->
        	<table border="0" style="background-color: #d5e2ee; border: 1px solid #8baed1; margin-left: 10px; margin-top: 5px;" width="700px;">
            	<tr><th colspan="2">Maintenance</h></tr>
            	<tr><td><a href="?action=list_languages">List available languages</a></td><td><a href="index.php">Translate</a></tr>
                <tr><td><a href="?action=add_language">Add new language</a></td><td></td></tr>
            </table>
        </td></tr>
    </table>
    <?

    switch ($action) {
        case 'list_languages':
            Maintenance::listLanguages();
            break;

        case 'add_language':
            Maintenance::languageForm();
            break;

        case 'do_add_language':
            if (Maintenance::doAddLanguage()) {
                Maintenance::listLanguages();
            }
            break;
    }
}




?>