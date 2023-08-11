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
        self::RULE_DIGIT => 'This field must contain a a number.i.e 0-9.',
        self::RULE_NODIGIT => 'This field should not cantain any numerical characters.',
        self::RULE_UNIQUE => 'Great minds think a like this {record} already exists in our database kindly choose a different {record}.',
    ];
    public const RULE_REQUIRED = 1;
    public const RULE_MIN = 2;
    public const RULE_MAX = 3;
    public const RULE_MATCH = 4;
    public const RULE_EMAIL = 5;
    public const RULE_UNIQUE = 6;
    public const RULE_SPECIAL = 7;
    public const RULE_NOTSPECIAL = 8;
    public const RULE_DIGIT = 9;
    public const RULE_NODIGIT = 10;
    public const RULE_SPACE = 11;
    public const RULE_NOSPACE = 12;
    public const RULE_LOWERCASE = 13;
    public const RULE_UPPERCASE = 14;

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
                if (
                    ($ruleName === self::RULE_REQUIRED && !$value)
                    || ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL))

                ) {
                    $this->addError($nameAttr, $ruleName);
                    break;
                } elseif (
                    ($ruleName === self::RULE_MIN && strlen($value) < $rule['min'])
                    || ($ruleName === self::RULE_MAX && strlen($value) > $rule['max'])
                ) {
                    $this->addError($nameAttr, $ruleName, $rule);
                    break;
                } elseif (($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']})) {
                    $rule['match'] = $this->label($rule['match']);
                    $this->addError($nameAttr, $ruleName, $rule);
                    break;
                } else {
                    $speical = preg_match('/[\'^£!$%&*()}{@#~?><>,|=_+¬-]/', $value);
                    if (
                        ($ruleName === self::RULE_SPECIAL && !$speical)
                        || ($ruleName === self::RULE_NOTSPECIAL && $speical)
                    ) {
                        $this->addError($nameAttr, $ruleName);
                        break;
                    }
                    $digit = preg_match("/\d/", $value);
                    if (
                        ($ruleName === self::RULE_DIGIT && !$digit)
                        || ($ruleName === self::RULE_NODIGIT && $digit)
                    ) {
                        $this->addError($nameAttr, $ruleName);
                        break;
                    }

                    if (
                        ($ruleName === self::RULE_LOWERCASE && !preg_match(
                            "/[a-z]/",
                            $value
                        )
                        )
                        || ($ruleName === self::RULE_UPPERCASE && !preg_match("/[A-Z]/", $value))
                    ) {
                        $this->addError($nameAttr, $ruleName);
                        break;
                    }
                    $space = preg_match('/\s/', $value);
                    if (
                        ($ruleName === self::RULE_SPACE && !$space)
                        || ($ruleName === self::RULE_NOSPACE && $space)
                    ) {
                        $this->addError($nameAttr, $ruleName);
                        break;
                    }
                    if ($ruleName === self::RULE_UNIQUE) {
                        $stmt = Application::app()->db->select($rule['table'], [
                            $rule['column'] => $value
                        ]);
                        if ($stmt->rowCount() > 0) {
                            $this->addError($nameAttr, $ruleName, ['record' => $this->label($nameAttr)]);
                            break;
                        }
                    }
                }
            }
        }
        return empty($this->errors);
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