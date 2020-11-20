<?php
    /**
     * Validator
     * @author Don de Dieu BOLENGE <dondedieubolenge@gmail.com>
     */
    namespace Ekolo\Builder\Http;

    use Ekolo\Builder\Http\Request;

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
         * Lance la validation des données
         * @return void
         */
        public function validator()
        {
            $this->verify();

            if ($this->hasErrors()) {
                \session('errors', $this->errors);
            }
            
            return !$this->hasErrors();
        }

        /**
         * Vérifie les règles pour valider la requête envoyée
         * @return void
         */
        public function verify()
        {
            if (!empty($this->rules)) {
                $fields = \array_keys($this->rules);
                $values = [];

                foreach ($fields as $field) {
                    if ($this->body()->has($field)) {
                        $fieldRules = $this->rules[$field];
                        $fieldRules = \explode('|', $fieldRules);

                        foreach ($fieldRules as $aRule) {
                            $tabARule = \explode(':', $aRule);
                            $fieldValue = $this->body()->$field();
                            $regle = $tabARule[0];

                            if (!is_callable([$this, $regle])) {
                                throw new \Exception('La règle '.$regle.' n\'est pas (plus) pris en charge');
                            }

                            if (in_array('required', $fieldRules) || !empty($fieldValue)) {

                                if (count($tabARule) > 1) {
                                    $param = $tabARule[1];

                                    $this->$regle($field, $fieldValue, $param);
                                }else {
                                    $this->$regle($field, $fieldValue);
                                }
                            }
                        }
                    }
                }
            }
        }

        /**
         * Pour les champs recquis
         * @param string $field Le champ
         * @param mixed $value La valeur du champ
         */
        public function required(string $field, $value)
        {
            $value = \trim($value);

            if (\strlen($value) <= 0) {
                $this->addError($field, "champ obligatoire");
            }
        }

        /**
         * Ajoute l'erreur
         * @param string $field Le champ
         * @param string $error L'erreur à ajouter
         */
        public function addError(string $field, string $error) {
            $this->errors[$field] = !empty($this->errors[$field]) 
                                    ? $this->errors[$field].', '.$error 
                                    : $field.' '.$error;
        }

        /**
         * Vérifie s'y a des erreurs
         * @return bool
         */
        public function hasErrors()
        {
            return !empty($this->errors);
        }

        /**
         * Vérifie si la valeur du champ est une adresse email valide
         * @param string $field
         * @param mixed $value
         * @return void
         */
        public function email(string $field, $value) 
        {
            if (!\is_email_valid($value)) {
                $this->addError($field, "doit être une adresse email valide");
            }
        }

        /**
         * Vérifie si le champ est max qu'il faut
         * @param string $field Le nom du champ en question
         * @param string $value La valeur du champ
         * @return bool
         */
        public function int($field, $value) 
        {
            $value = (string) $value;
            $value = trim($value);
    
            if (!\is_int_valid($value)) {
                $this->addError($field, "doit être un entier");
            }
        }

        /**
         * Vérifie si le champ est au minimum qu'il faut
         * @param string $field Le nom du champ en question
         * @param string $value La valeur du champ
         * @param int $minVal La valeur maximum que doit avoir le champ
         * @return void
         */
        public function min(string $field, $value, $minVal) {
            $value = trim($value);
            $minVal = (int) $minVal;

            if (\strlen($value) < $minVal) {
                $error = "minimum " . $minVal . " caractère" . make_to_pluriel($minVal);
                
                $this->addError($field, $error);
            }
        }

        /**
         * Vérifie si le nombre de caractère du champ est au maximum qu'il faut
         * @param string $field Le nom du champ en question
         * @param string $value La valeur du champ
         * @param int $max La valeur maximum que doit avoir le champ
         * @return void
         */
        public function max(string $field, $value, $max) {
            $value = trim($value);
            $max = (int) $max;

            if (\strlen($value) > $max) {
                $error = "maximum " . $max . " caractère" . make_to_pluriel($max);
                
                $this->addError($field, $error);
            }
        }

        /**
         * Vérifie si le nombre de caractère du champ est au maximum qu'il faut
         * @param string $field Le nom du champ en question
         * @param string $value La valeur du champ
         * @param int $length La valeur maximum que doit avoir le champ
         * @return void
         */
        public function alpha(string $field, $value, $length = null) {
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
                $this->addError($field, $error);
            }
        }

        /**
         * Vérifie si le champ est un numero de téléphone valide
         * @param string $field Le nom du champ en question
         * @param string $value La valeur du champ
         * @return bool
         */
        public function tel($field, $value) 
        {
            $value = (string) $value;
            $value = trim($value);
    
            if (!is_tel($value)) {
                $this->addError($field, "doit être (ex: +24389... ou 089...)");
            }
        }

        /**
         * Vérifie si le champ est numérique
         * @param string $field Le nom du champ en question
         * @param string $value La valeur du champ
         * @return void
         */
        public function numeric($field, $value) 
        {
            $value = (string) $value;
            $value = trim($value);
    
            if (!is_numeric($value)) {
                $this->addError($field, "doit être numérique");
            }
        }

        public function body()
        {
            return $this->body;
        }
    }
    