<?php
    /**
     * Validator
     * @author Don de Dieu BOLENGE <dondedieubolenge@gmail.com>
     */
    namespace Ekolo\Builder\Http;

    use Ekolo\Builder\Http\Request;
    use Ekolo\Http\Options\Bodies;

    /**
     * 
     */
    class Validator
    {
        /**
         * Les règles à valider
         * @var array
         */
        protected $rules = [];

        protected $body;

        /**
         * Tableau des erreurs
         * @var array
         */
        protected $errors = [];

        public function __construct(Request $request)
        {
            $this->body = $request->body();
        }

        /**
         * Check the rules to validate the sent request
         * @param array $rules Validator rules
         * @return void
         */
        public function verify(array $rules)
        {
            $this->setRules($rules);

            if (!empty($this->rules)) {
                $fields = \array_keys($this->rules);
                $values = [];
                $field_front = null;

                foreach ($fields as $field) {
                    if ($this->body()->has($field)) {
                        $fieldRules = $this->rules[$field];

                        if (preg_match("#(field:.+)#i", $fieldRules, $machedFieldFront)) {
                            $fieldRules = \str_replace($machedFieldFront[1], '',$fieldRules);
                            $field_front = \explode(':', $machedFieldFront[1])[1];
                        }else {
                            $field_front = null;
                        }
                        
                        $fieldRules = \explode('|', $fieldRules);

                        foreach ($fieldRules as $aRule) {
                            $tabARule = \explode(':', $aRule);
                            $fieldValue = $this->body()->$field();
                            $regle = $tabARule[0];
                            
                            if (!empty($regle)) {
                                if (!is_callable([$this, $regle])) {
                                    throw new \Exception('La règle '.$regle.' n\'est pas (plus) pris en charge');
                                }

                                if (in_array('required', $fieldRules) || !empty($fieldValue)) {

                                    if (count($tabARule) > 1) {
                                        $param = $tabARule[1];

                                        $this->$regle($field, $fieldValue, $param, $field_front);
                                    }else {
                                        $this->$regle($field, $fieldValue, $field_front);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ($this->hasErrors()) {
                \session('errors', $this->errors);
            }
            
            return !$this->hasErrors();
        }

        /**
         * For the required fields
         * @param string $field
         * @param mixed $value
         * @param string $fieldFront The name of the field in the error message
         */
        public function required(string $field, $value, string $fieldFront = null)
        {
            $value = \trim($value);

            if (\strlen($value) <= 0) {
                $this->addError($field, "champ obligatoire", $fieldFront);
            }
        }

        /**
         * Allows you to add tracked errors
         * @param string $field The field where there is an error
         * @param string $error The error to add
         * @param string $fieldFront The name of the field in the error message
         * @return void
         */
        public function addError(string $field, string $error, string $fieldFront = null) {
            $fieldFront = !empty($fieldFront) ? $fieldFront : $field;

            $this->errors[$field] = !empty($this->errors[$field]) 
                                    ? $this->errors[$field].', '.$error 
                                    : $fieldFront.' '.$error;
        }

        /**
         * Check if there are any errors in the validation made
         * @return bool
         */
        public function hasErrors()
        {
            return !empty($this->errors);
        }

        /**
         * Checks if the field value is a valid email address
         * @param string $field Name of this field
         * @param string $value His value
         * @param string $fieldFront The name of the field in the error message
         * @return void
         */
        public function email(string $field, $value, string $fieldFront = null)
        {
            if (!\is_email_valid($value)) {
                $this->addError($field, "doit être une adresse email valide", $fieldFront);
            }
        }

        /**
         * Checks if the field is an integer
         * @param string $field Name of this field
         * @param string $value His value
         * @param string $fieldFront The name of the field in the error message
         * @return bool
         */
        public function int($field, $value, string $fieldFront = null)
        {
            $value = (string) $value;
            $value = trim($value);
    
            if (!\is_int_valid($value)) {
                $this->addError($field, "doit être un entier", $fieldFront);
            }
        }

        /**
         * Checks if the field is at the minimum required
         * @param string $field Name of this field
         * @param string $value His value
         * @param int $minVal The maximum value that the field must have
         * @param string $fieldFront The name of the field in the error message
         * @return void
         */
        public function min(string $field, $value, $minVal, string $fieldFront = null) {
            $value = trim($value);
            $minVal = (int) $minVal;

            if (\strlen($value) < $minVal) {
                $error = "minimum " . $minVal . " caractère" . make_to_pluriel($minVal);
                
                $this->addError($field, $error, $fieldFront);
            }
        }

        /**
         * Checks if the number of characters in the field is the maximum required
         * @param string $field The name of this field
         * @param string $value His value
         * @param int $max The maximum value that the field must have
         * @param string $fieldFront The name of the field in the error message
         * @return void
         */
        public function max(string $field, $value, $max, string $fieldFront = null) {
            $value = trim($value);
            $max = (int) $max;

            if (\strlen($value) > $max) {
                $error = "maximum " . $max . " caractère" . make_to_pluriel($max);
                
                $this->addError($field, $error, $fieldFront);
            }
        }

        /**
         * Checks if the number of characters in the field is the maximum required
         * @param string $field The name of this field
         * @param string $value his value
         * @param int $length The maximum value that the field must have
         * @param string $fieldFront The name of the field in the error message
         * @return void
         */
        public function alpha(string $field, $value, $length = null, string $fieldFront = null) {
            $value = trim($value);
            $length = (int) $length;
            $error = '';

            if (\is_numeric($value)) {
                $error = "doit être alphanumérique";

            }

            if (!empty($length)) {
                if (strlen($value) != $length) {
                    $error .= !empty($error) ? ' et avoir ' : 'doit avoir ';
                    $error .= $length.' caractère'.make_to_pluriel($length);
                }
            }

            if (!empty($error)) {
                $this->addError($field, $error, $fieldFront);
            }
        }

        /**
         * Checks if the field is a valid phone number
         * @param string $field The name of the field
         * @param string $value The value of the field
         * @param string $fieldFront The name of the field in the error message
         * @return bool
         */
        public function tel(string $field, $value, string $fieldFront = null)
        {
            $value = (string) $value;
            $value = trim($value);
    
            if (!is_tel($value)) {
                $this->addError($field, "doit être (ex: +24389... ou 089...)", $fieldFront);
            }
        }

        /**
         * Check if the field's numeric
         * @param string $field
         * @param string $value
         * @param string $fieldFront The name of the field in the error message
         * @return void
         */
        public function numeric(string $field, $value, string $fieldFront = null)
        {
            $value = (string) $value;
            $value = trim($value);
    
            if (!is_numeric($value)) {
                $this->addError($field, "doit être numérique", $fieldFront);
            }
        }

        /**
         * Return the Bodies instance
         * @return Bodies
         */
        public function body()
        {
            return $this->body;
        }

        /**
         * Modify the rules value
         * @param array $rules
         * @return void
         */
        public function setRules(array $rules = [])
        {
            $this->rules = $rules;
        }

        /**
         * Allows you to change the field name in the output error message
         * @param string $field The name of this field
         * @param string $value His value
         * @param int $max The maximum value that the field must have
         * @return void
         */
        public function field(string $name) {
            $value = trim($value);
            $max = (int) $max;

            if (\strlen($value) > $max) {
                $error = "maximum " . $max . " caractère" . make_to_pluriel($max);
                
                $this->addError($field, $error, $fieldFront);
            }
        }
    }
    