<?php
namespace MuhthishimisCoding\PreFramework;

abstract class Model
{
    public array $errors = [];
    public array $errorMessages = [
        self::RULE_REQUIRED => 'This is a required field.',
        self::RULE_EMAIL => 'Kindly provide a valid Email.',
        self::RULE_MATCH => 'This should match with the {match}.',
        self::RULE_MIN => 'This field should contain minimum {min} characters.',
        self::RULE_MAX => 'This field should contain maximum {max} characters.',
        self::RULE_SPECIAL => 'This field must contain a special character i.e. $,#,%,@ etc.',
        self::RULE_NOTSPECIAL => 'This field should not contain any special characters i.e.$,#,&,@,> etc.',
        self::RULE_SPACE => 'This field must contain a space.',
        self::RULE_NOSPACE => 'This field should not contain a space.',
        self::RULE_LOWERCASE => 'This field must contain a lowercase character.',
        self::RULE_UPPERCASE => 'This field must contain a uppercase character.',
        self::RULE_DIGIT => 'This field must contain a number.i.e 0-9.',
        self::RULE_ONLYDIGIT => 'This field should only contain digits',
        self::RULE_NODIGIT => 'This field should not cantain any numerical characters.',
        self::RULE_UNIQUE => 'Great minds think a like this {record} already exists in our database kindly choose a different {record}.',
        self::RULE_MAXNUMB => 'The maximum number of {maxplaceholder} is up to {max}.',
    ];
    public const RULE_REQUIRED = 'required';
    public const RULE_MIN = 'minimum';
    public const RULE_MAX = 'maximum';
    public const RULE_MATCH = 'matchcase';
    public const RULE_EMAIL = 'email';
    public const RULE_UNIQUE = 'unique';
    public const RULE_SPECIAL = 'special';
    public const RULE_NOTSPECIAL = 'no_special';
    public const RULE_DIGIT = 'digit';
    public const RULE_NODIGIT = 'no_digit';
    public const RULE_SPACE = 'space';
    public const RULE_NOSPACE = 'no_space';
    public const RULE_LOWERCASE = 'lowercase';
    public const RULE_UPPERCASE = 'uppercase';
    public const RULE_ONLYDIGIT = 'only_digit';
    public const RULE_MAXNUMB = 'mamnumb';

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
    abstract public function rules(): array;
    public function validate()
    {
        foreach ($this->rules() as $nameAttr => $rules) {
            $value = $this->{$nameAttr};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (is_array($rule)) {
                    $ruleName = $rule[0];
                }
                if (method_exists($this, $ruleName)) {
                    if ($this->{$ruleName}($value, $nameAttr, $rule)) {
                        break;
                    }
                }
            
            }
        }
        return empty($this->errors);
    }
    protected function required($value, $nameAttr, $rule)
    {
        if (!$value) {
            return $this->addError($nameAttr, self::RULE_REQUIRED);
        }
    }
    protected function email($value, $nameAttr, $rule)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $this->addError($nameAttr, self::RULE_EMAIL);
        }
    }
    protected function minimum($value, $nameAttr, $rule)
    {
        if (strlen($value) < $rule['min']) {
            return $this->addError($nameAttr, self::RULE_MIN, $rule);
        }
    }
    protected function maximum($value, $nameAttr, $rule)
    {
        if (strlen($value) > $rule['max']) {
            return $this->addError($nameAttr, self::RULE_MAX, $rule);
        }
    }
    protected function mamnumb($value, $nameAttr, $rule)
    {
        if ((int) $value > $rule['max']) {
            return $this->addError($nameAttr, self::RULE_MAXNUMB, $rule);
        }
    }
    protected function matchcase($value, $nameAttr, $rule)
    {
        if ($value !== $this->{$rule['match']}) {
            $rule['match'] = $this->label($rule['match']);
            return $this->addError($nameAttr, self::RULE_MATCH, $rule);
        }
    }
    protected function special($value, $nameAttr, $rule)
    {
        if (!preg_match('/[\'^£!$%&*()}{@#~?><>,|=_+¬-]/', $value)) {
            return $this->addError($nameAttr, self::RULE_SPECIAL);
        }
    }
    protected function no_special($value, $nameAttr, $rule)
    {
        if (preg_match('/[\'^£!$%&*()}{@#~?><>,|=_+¬-]/', $value)) {
            return $this->addError($nameAttr, self::RULE_NOTSPECIAL);
        }
    }
    protected function digit($value, $nameAttr, $rule)
    {
        if (!preg_match("/\d/", $value)) {
            return $this->addError($nameAttr, self::RULE_DIGIT);

        }
    }
    protected function no_digit($value, $nameAttr, $rule)
    {
        if (preg_match("/\d/", $value)) {
            return $this->addError($nameAttr, self::RULE_NODIGIT);
        }
    }
    protected function only_digit($value, $nameAttr, $rule)
    {
        if (
            !preg_match("/\d/", $value)
            || preg_match('/[\'^£!$%&*()}{@#~?><>,|=_+¬-]/', $value)
            || preg_match("/[a-z]/", $value)
            || preg_match("/[A-Z]/", $value)
        ) {
            return $this->addError($nameAttr, self::RULE_ONLYDIGIT);
        }
    }
    protected function space($value, $nameAttr, $rule)
    {
        if (!preg_match('/\s/', $value)) {
            return $this->addError($nameAttr, self::RULE_SPACE);
        }
    }
    protected function no_space($value, $nameAttr, $rule)
    {
        if (preg_match('/\s/', $value)) {
            return $this->addError($nameAttr, self::RULE_NOSPACE);

        }
    }
    protected function lowercase($value, $nameAttr, $rule)
    {
        if (!preg_match("/[a-z]/", $value)) {
            return $this->addError($nameAttr, self::RULE_LOWERCASE);

        }
    }

    protected function uppercase($value, $nameAttr, $rule)
    {
        if (!preg_match("/[A-Z]/", $value)) {
            return $this->addError($nameAttr, self::RULE_UPPERCASE);
        }
    }
    protected function unique($value, $nameAttr, $rule)
    {
        $stmt = Application::app()->db->select($rule['table'], [
            $rule['column'] => $value
        ]);
        if ($stmt->rowCount() > 0) {
            return $this->addError($nameAttr, self::RULE_UNIQUE, ['record' => $this->label($nameAttr)]);
        }
        return false;
    }
    public function label($nameAttr): string
    {
        return $this->labels[$nameAttr] ?? $nameAttr;
    }
    public function addError(string $attribute, $ruleName, $params = [])
    {
        $message = $this->errorMessages[$ruleName] ?? 'There is an unkown error occured.';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute] = $message;
        return true;
    }
    function hasError($attribute)
    {
        return isset($this->errors[$attribute]);
    }
    function getError($attribute)
    {
        return $this->errors[$attribute];
    }
    public function errorMessage($rule)
    {
        return $this->errorMessages[$rule];
    }
}