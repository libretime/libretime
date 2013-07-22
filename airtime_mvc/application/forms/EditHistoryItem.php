<?php

class Application_Form_EditHistoryItem extends Zend_Form
{
	const VALIDATE_DATETIME_FORMAT = 'yyyy-MM-dd HH-mm-ss';
	const VALIDATE_DATE_FORMAT = 'yyyy-MM-dd';
	const VALIDATE_TIME_FORMAT = 'HH-mm-ss';
	
	const ID_PREFIX = "his_item_";
	
	const ITEM_TYPE = "type";
	const ITEM_CLASS = "class";
	const ITEM_ID_SUFFIX = "name";
	
	private $formElTypes = array(
		TEMPLATE_DATE => array(
			"class" => "Zend_Form_Element_Text",
			"attrs" => array(
				"class" => "input_text"
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
				"class" => "input_text"
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
				"class" => "input_text"
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
				"class" => "input_text"
			),
			"filters" => array(
				"StringTrim"
			)
		),
		TEMPLATE_BOOLEAN => array(
			"class" => "Zend_Form_Element_Checkbox",
			"filters" => array(
				"Boolean"
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
				"class" => "input_text"
			),
			"filters" => array(
				"Int"
			)
		),
		TEMPLATE_FLOAT => array(
			"class" => "Zend_Form_Element_Text",
			"attrs" => array(
				"class" => "input_text"
			),
			"validators" => array(
				array(
					"class" => "Zend_Validate_Float",
				)
			)
		), 
	);
	
	public function init() {

	    $history_id = new Zend_Form_Element_Hidden(self::ID_PREFIX.'id');
	    $history_id->setValidators(array(
	        new Zend_Validate_Int()
	    ));
	    $this->addElement($history_id);
	}
	
	public function createFromTemplate($template) {
		
		for ($i = 0, $len = count($template); $i < $len; $i++) {
			
			$item = $template[$i];
			
			$formElType = $this->formElTypes[$item[self::ITEM_TYPE]];
			
			$label = $item[self::ITEM_ID_SUFFIX];
			$id = self::ID_PREFIX.$label;
			$el = new $formElType[self::ITEM_CLASS]($id);
			
			//cleaning up presentation of tag name for labels.
			$label = implode(" ", explode("_", $label));
			$label = ucwords($label);
			$el->setLabel(_($label));
			
			if (isset($formElType["attrs"])) {
				
				$attrs = $formElType["filters"];
				
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
					$validator = new $arr[self::ITEM_CLASS]();
					
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
			
			$this->addElement($el);
		}
		
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
		
		$this->addDisplayGroup(
			array(
				'his_item_save',
				'his_item_cancel'
			),
			'submitButtons',
			array(
				'decorators' => array(
					'FormElements',
					'DtDdWrapper'
				)
			)
		);	
	}
}