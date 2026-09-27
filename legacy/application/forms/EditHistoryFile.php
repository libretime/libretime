<?php

class Application_Form_EditHistoryFile extends Application_Form_EditHistory
{
    public const ID_PREFIX = 'his_file_';

    public function init(): void
    {
        parent::init();

        $this->setDecorators(
            [
                ['ViewScript', ['viewScript' => 'form/edit-history-file.phtml']],
            ]
        );
    }

    public function createFromTemplate($template, $required): void
    {
        parent::createFromTemplate($template, $required);
    }
}
