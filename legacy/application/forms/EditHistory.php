<?php

declare(strict_types=1);

class Application_Form_EditHistory extends Zend_Form
{
    public const VALIDATE_DATETIME_FORMAT = 'yyyy-MM-dd HH:mm:ss';
    // this is used by the javascript widget, unfortunately h/H is opposite from Zend.
    public const TIMEPICKER_DATETIME_FORMAT = 'yyyy-MM-dd hh:mm:ss';

    public const VALIDATE_DATE_FORMAT = 'yyyy-MM-dd';
    public const VALIDATE_TIME_FORMAT = 'HH:mm:ss';

    public const ITEM_TYPE = 'type';
    public const ITEM_CLASS = 'class';
    public const ITEM_OPTIONS = 'options';
    public const ITEM_ID_SUFFIX = 'name';

    public const TEXT_INPUT_CLASS = 'input_text';

    private $formElTypes = [
        TEMPLATE_DATE => [
            'class' => 'Zend_Form_Element_Text',
            'attrs' => [
                'class' => self::TEXT_INPUT_CLASS,
            ],
            'validators' => [
                [
                    'class' => 'Zend_Validate_Date',
                    'params' => [
                        'format' => self::VALIDATE_DATE_FORMAT,
                    ],
                ],
            ],
            'filters' => [
                'StringTrim',
            ],
        ],
        TEMPLATE_TIME => [
            'class' => 'Zend_Form_Element_Text',
            'attrs' => [
                'class' => self::TEXT_INPUT_CLASS,
            ],
            'validators' => [
                [
                    'class' => 'Zend_Validate_Date',
                    'params' => [
                        'format' => self::VALIDATE_TIME_FORMAT,
                    ],
                ],
            ],
            'filters' => [
                'StringTrim',
            ],
        ],
        TEMPLATE_DATETIME => [
            'class' => 'Zend_Form_Element_Text',
            'attrs' => [
                'class' => self::TEXT_INPUT_CLASS,
            ],
            'validators' => [
                [
                    'class' => 'Zend_Validate_Date',
                    'params' => [
                        'format' => self::VALIDATE_DATETIME_FORMAT,
                    ],
                ],
            ],
            'filters' => [
                'StringTrim',
            ],
        ],
        TEMPLATE_STRING => [
            'class' => 'Zend_Form_Element_Text',
            'attrs' => [
                'class' => self::TEXT_INPUT_CLASS,
            ],
            'filters' => [
                'StringTrim',
            ],
        ],
        TEMPLATE_BOOLEAN => [
            'class' => 'Zend_Form_Element_Checkbox',
            'validators' => [
                [
                    'class' => 'Zend_Validate_InArray',
                    'options' => [
                        'haystack' => [0, 1],
                    ],
                ],
            ],
        ],
        TEMPLATE_INT => [
            'class' => 'Zend_Form_Element_Text',
            'validators' => [
                [
                    'class' => 'Zend_Validate_Int',
                ],
            ],
            'attrs' => [
                'class' => self::TEXT_INPUT_CLASS,
            ],
        ],
        TEMPLATE_FLOAT => [
            'class' => 'Zend_Form_Element_Text',
            'attrs' => [
                'class' => self::TEXT_INPUT_CLASS,
            ],
            'validators' => [
                [
                    'class' => 'Zend_Validate_Float',
                ],
            ],
        ],
    ];

    public function init()
    {
        $history_id = new Zend_Form_Element_Hidden($this::ID_PREFIX . 'id');
        $history_id->setValidators([
            new Zend_Validate_Int(),
        ]);
        $history_id->setDecorators(['ViewHelper']);
        $this->addElement($history_id);

        $dynamic_attrs = new Zend_Form_SubForm();
        $this->addSubForm($dynamic_attrs, $this::ID_PREFIX . 'template');

        // Add the submit button
        $this->addElement('button', $this::ID_PREFIX . 'save', [
            'ignore' => true,
            'class' => 'btn ' . $this::ID_PREFIX . 'save',
            'label' => _('Save'),
            'decorators' => [
                'ViewHelper',
            ],
        ]);

        // Add the cancel button
        $this->addElement('button', $this::ID_PREFIX . 'cancel', [
            'ignore' => true,
            'class' => 'btn ' . $this::ID_PREFIX . 'cancel',
            'label' => _('Cancel'),
            'decorators' => [
                'ViewHelper',
            ],
        ]);
    }

    public function createFromTemplate($template, $required)
    {
        $templateSubForm = $this->getSubForm($this::ID_PREFIX . 'template');

        for ($i = 0, $len = count($template); $i < $len; ++$i) {
            $item = $template[$i];
            // don't dynamically add this as it should be included in the
            // init() function already if it should show up in the UI..
            if (in_array($item['name'], $required)) {
                continue;
            }

            $formElType = $this->formElTypes[$item[self::ITEM_TYPE]];

            $label = $item[self::ITEM_ID_SUFFIX];
            $id = $this::ID_PREFIX . $label;
            $el = new $formElType[self::ITEM_CLASS]($id);
            $el->setLabel($item['label']);

            if (isset($formElType['attrs'])) {
                $attrs = $formElType['attrs'];

                foreach ($attrs as $key => $value) {
                    $el->setAttrib($key, $value);
                }
            }

            if (isset($formElType['filters'])) {
                $filters = $formElType['filters'];

                foreach ($filters as $filter) {
                    $el->addFilter($filter);
                }
            }

            if (isset($formElType['validators'])) {
                $validators = $formElType['validators'];

                foreach ($validators as $index => $arr) {
                    $options = isset($arr[self::ITEM_OPTIONS]) ? $arr[self::ITEM_OPTIONS] : null;
                    $validator = new $arr[self::ITEM_CLASS]($options);

                    // extra validator info
                    if (isset($arr['params'])) {
                        foreach ($arr['params'] as $key => $value) {
                            $method = 'set' . ucfirst($key);
                            $validator->{$method}($value);
                        }
                    }

                    $el->addValidator($validator);
                }
            }

            $el->setDecorators(['ViewHelper']);
            $templateSubForm->addElement($el);
        }
    }
}
