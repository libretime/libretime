<?php

class Application_Form_EditHistoryItem extends Zend_Form
{
	const VALIDATE_DATETIME_FORMAT = 'yyyy-MM-dd HH:mm:ss';
	//this is used by the javascript widget, unfortunately h/H is opposite from Zend.
	const TIMEPICKER_DATETIME_FORMAT = 'yyyy-MM-dd hh:mm:ss';

	const VALIDATE_DATE_FORMAT = 'yyyy-MM-dd';
	const VALIDATE_TIME_FORMAT = 'HH:mm:ss';

	const ID_PREFIX = "his_item_";

	const ITEM_TYPE = "type";
	const ITEM_CLASS = "class";
	const ITEM_OPTIONS = "options";
	const ITEM_ID_SUFFIX = "name";

	const TEXT_INPUT_CLASS = "input_text";

	private $formElTypes = array(
		TEMPLATE_DATE => array(
			"class" => "Zend_Form_Element_Text",
			"attrs" => array(
				"class" => self::TEXT_INPUT_CLASS
			),
			"validators" => array(
				array(
					"class" => "Zend_Validate_Date",
					"params" => array(
						"format" => self::VALIDATE_DATE_FORMAT
					)
				)
			),
			"filters" => array(
				"StringTrim"
			)
		),
		TEMPLATE_TIME => array(
			"class" => "Zend_Form_Element_Text",
			"attrs" => array(
				"class" => self::TEXT_INPUT_CLASS
			),
			"validators" => array(
				array(
					"class" => "Zend_Validate_Date",
					"params" => array(
						"format" => self::VALIDATE_TIME_FORMAT
					)
				)
			),
			"filters" => array(
				"StringTrim"
			)
		),
		TEMPLATE_DATETIME => array(
			"class" => "Zend_Form_Element_Text",
			"attrs" => array(
				"class" => self::TEXT_INPUT_CLASS
			),
			"validators" => array(
				array(
					"class" => "Zend_Validate_Date",
					"params" => array(
						"format" => self::VALIDATE_DATETIME_FORMAT
					)
				)
			),
			"filters" => array(
				"StringTrim"
			)
		),
		TEMPLATE_STRING => array(
			"class" => "Zend_Form_Element_Text",
			"attrs" => array(
				"class" => self::TEXT_INPUT_CLASS
			),
			"filters" => array(
				"StringTrim"
			)
		),
		TEMPLATE_BOOLEAN => array(
			"class" => "Zend_Form_Element_Checkbox",
			"validators" => array(
				array(
					"class" => "Zend_Validate_InArray",
					"options" => array(
						"haystack" => array(0,1)
					)
				)
			)
		),
		TEMPLATE_INT => array(
			"class" => "Zend_Form_Element_Text",
			"validators" => array(
				array(
					"class" => "Zend_Validate_Int",
				)
			),
			"attrs" => array(
				"class" => self::TEXT_INPUT_CLASS
			)
		),
		TEMPLATE_FLOAT => array(
			"class" => "Zend_Form_Element_Text",
			"attrs" => array(
				"class" => self::TEXT_INPUT_CLASS
			),
			"validators" => array(
				array(
					"class" => "Zend_Validate_Float",
				)
			)
		),
	);

	public function init() {

		$this->setDecorators(array(
			'PrepareElements',
			array('ViewScript', array('viewScript' => 'form/edit-history-item.phtml'))
		));

	    $history_id = new Zend_Form_Element_Hidden(self::ID_PREFIX.'id');
	    $history_id->setValidators(array(
	        new Zend_Validate_Int()
	    ));
	    $history_id->setDecorators(array('ViewHelper'));
	    $this->addElement($history_id);

	    $starts = new Zend_Form_Element_Text(self::ID_PREFIX.'starts');
	    $starts->setValidators(array(
	    	new Zend_Validate_Date(self::VALIDATE_DATETIME_FORMAT)
	    ));
	    $starts->setAttrib('class', self::TEXT_INPUT_CLASS);
	    $starts->setAttrib('data-format', self::TIMEPICKER_DATETIME_FORMAT);
	    $starts->addFilter('StringTrim');
	    $starts->setLabel(_('Start Time'));
	    $starts->setDecorators(array('ViewHelper'));
	    $starts->setRequired(true);
	    $this->addElement($starts);

	    $ends = new Zend_Form_Element_Text(self::ID_PREFIX.'ends');
	    $ends->setValidators(array(
	    	new Zend_Validate_Date(self::VALIDATE_DATETIME_FORMAT)
	    ));
	    $ends->setAttrib('class', self::TEXT_INPUT_CLASS);
	    $ends->setAttrib('data-format', self::TIMEPICKER_DATETIME_FORMAT);
	    $ends->addFilter('StringTrim');
	    $ends->setLabel(_('End Time'));
	    $ends->setDecorators(array('ViewHelper'));
	    $ends->setRequired(true);
	    $this->addElement($ends);

	    $dynamic_attrs = new Zend_Form_SubForm();
	    $this->addSubForm($dynamic_attrs, self::ID_PREFIX.'template');

	    // Add the submit button
	    $this->addElement('button', 'his_item_save', array(
    		'ignore'   => true,
    		'class'    => 'btn his_item_save',
    		'label'    => _('Save'),
    		'decorators' => array(
    			'ViewHelper'
    		)
	    ));

	    // Add the cancel button
	    $this->addElement('button', 'his_item_cancel', array(
    		'ignore'   => true,
    		'class'    => 'btn his_item_cancel',
    		'label'    => _('Cancel'),
    		'decorators' => array(
    			'ViewHelper'
    		)
	    ));
	}

	public function createFromTemplate($template, $required) {

		$templateSubForm = $this->getSubForm(self::ID_PREFIX.'template');

		for ($i = 0, $len = count($template); $i < $len; $i++) {

			$item = $template[$i];
			//don't dynamically add this as it should be included in the init() function already.
			if (in_array($item["name"], $required)) {
				continue;
			}

			$formElType = $this->formElTypes[$item[self::ITEM_TYPE]];

			$label = $item[self::ITEM_ID_SUFFIX];
			$id = self::ID_PREFIX.$label;
			$el = new $formElType[self::ITEM_CLASS]($id);

			//cleaning up presentation of tag name for labels.
			$label = implode(" ", explode("_", $label));
			$label = ucwords($label);
			$el->setLabel(_($label));

			if (isset($formElType["attrs"])) {

				$attrs = $formElType["attrs"];

				foreach ($attrs as $key => $value) {
					$el->setAttrib($key, $value);
				}
			}

			if (isset($formElType["filters"])) {

				$filters = $formElType["filters"];

				foreach ($filters as $filter) {
					$el->addFilter($filter);
				}
			}

			if (isset($formElType["validators"])) {

				$validators = $formElType["validators"];

				foreach ($validators as $index => $arr) {
					$options = isset($arr[self::ITEM_OPTIONS]) ? $arr[self::ITEM_OPTIONS] : null;
					$validator = new $arr[self::ITEM_CLASS]($options);

					//extra validator info
					if (isset($arr["params"])) {

						foreach ($arr["params"] as $key => $value) {
							$method = "set".ucfirst($key);
							$validator->$method($value);
						}
					}

					$el->addValidator($validator);
				}
			}

			$el->setDecorators(array('ViewHelper'));
			$templateSubForm->addElement($el);
		}	
	}
	
	public function fillFields() {
			
	}
}