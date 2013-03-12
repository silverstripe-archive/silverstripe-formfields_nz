<?php

/**
 * A {@link FormField} containing 3 individual fields for a users New Zealand
 * based IRD number
 *
 * @package formfields_nz
 */
class IrdNumberField extends TextField {
	
	public function Field() {
		$valArr = ($this->value) ? explode('-', $this->value) : null;

		// fields
		$first = new NumericField($this->name . '[first]', false, ($valArr) ? array_shift($valArr) : null);
		$first->setMaxLength(3);
		$first->addExtraClass('ird-numeric');
	
		$second = new NumericField($this->name . '[second]', false, ($valArr) ? array_shift($valArr) : null);
		$second->setMaxLength(3);
		$second->addExtraClass('ird-numeric');

		$third = new NumericField($this->name . '[third]', false, ($valArr) ? array_shift($valArr) : null);
		$third->setMaxLength(3);
		$third->addExtraClass('ird-numeric');

		$fields = array(
			$first->Field(),
			$second->Field(),
			$third->Field()
		);

		$html = implode('<span style="padding: 0 8px">-</span>', $fields);
		
		return $html;
	}
		

	public function setValue($val) {
		if(empty($val)) {
			$this->value = null;
		} else {
			if(is_array($val)) {
				$this->value = implode('-', $val);
			}
			else {
				$this->value = $val;
			}
		}
	}

	public function jsValidation() {
		$formID = $this->form->FormName();
		$jsFunc = <<<JS
Behaviour.register({
	"#$formID": {
		validateIrdNumber: function(fieldName) {
			var value = \$F(_CURRENT_FORM.elements[fieldName+'[first]']);
			value += '-' + \$F(_CURRENT_FORM.elements[fieldName+'[second]']);
			value += '-' + \$F(_CURRENT_FORM.elements[fieldName+'[third]']);

			if(value.length > 0 && !value.match(/^[0-9]{2,3}[\-]?[0-9]{3}[\-]?[0-9]{3}\$/)) {
				validationError(el,"$error","validation",false);
				return false;
			}
			
			return true;
		}
	}
});
JS;
		Requirements::customScript($jsFunc, 'func_validateIrd_'.$formID);

		return <<<JS
	if(\$('$formID')){
		if(typeof fromAnOnBlur != 'undefined'){
			if(fromAnOnBlur.name == '$this->name')
				\$('$formID').validateIrdNumber('$this->name');
		}else{
			\$('$formID').validateIrdNumber('$this->name');
		}
	}
JS;
	}

	/**
	 * @return boolean
	 */
	public function validate($validator) {
		$valid = preg_match(
			'/^[0-9]{2}[\-]?[0-9]{3}[\-]?[0-9]{3}$/',
			$this->value
		);
		
		if(!$valid){
			$validator->validationError(
				$this->name, 
				_t('IrdNumberField.VALIDATION', "Please enter a valid IRD Number"),
				"validation", 
				false
			);

			return false;
		}
	}
}