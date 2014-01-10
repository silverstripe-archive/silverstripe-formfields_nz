<?php

/**
 * A {@link FormField} containing the validation for a New Zealand Drivers License
 *
 * @package formfields_nz
 */
class DriversLicenseNumberField extends TextField {
	
	public function __construct($name, $title = null, $value = "", $maxLength = null, $form = null) {
		parent::__construct($name, $title, $value, 8, $form);
	}

	public function jsValidation() {
		$formID = $this->form->FormName();
		$jsFunc = <<<JS
Behaviour.register({
	"#$formID": {
		validateDriversLicenseNumber: function(fieldName) {
			var value = \$F(_CURRENT_FORM.elements[fieldName]);

			if(value.length > 0 && !value.match(/^[A-z]{2}[0-9]{6}\$/)) {
				validationError(el,"$error","validation",false);
				return false;
			}
			
			return true;
		}
	}
});
JS;
		Requirements::customScript($jsFunc, 'func_validateDriver_'.$formID);

		return <<<JS
	if(\$('$formID')){
		if(typeof fromAnOnBlur != 'undefined'){
			if(fromAnOnBlur.name == '$this->name')
				\$('$formID').validateDriversLicenseNumber('$this->name');
		}else{
			\$('$formID').validateDriversLicenseNumber('$this->name');
		}
	}
JS;
	}

	/**
	 * @return boolean
	 */
	public function validate($validator) {
		if(!$this->value && !$validator->fieldIsRequired($this->name)) {
			return true;
		}
		
		$valid = preg_match(
			'/^[A-z]{2}[0-9]{6}$/',
			$this->value
		);
		
		if(!$valid){
			$validator->validationError(
				$this->name, 
				_t('DriversLicenseNumberField.VALIDATION', "Please enter a valid NZ drivers license number (e.g DD123456)"),
				"validation", 
				false
			);

			return false;
		}

		return true;
	}
}
