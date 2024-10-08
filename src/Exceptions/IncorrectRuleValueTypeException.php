<?php

namespace Arielenter\Validation\Exceptions;

use Arielenter\Validation\Constants\SupportedRuleClasses;
use Arielenter\Validation\Constants\TransPrefix;
use Illuminate\Contracts\Validation\Rule;
use TypeError;
use function __;

class IncorrectRuleValueTypeException extends TypeError {

    use TransPrefix,
        SupportedRuleClasses;

    public function __construct(
            mixed $validationRule,
            mixed $ruleValue,
            string $valueType,
            array $correctTypesAndClasses
    ) {
        $message = __(
                $this::TRANS_PREFIX . 'incorrect_rule_value_type',
                [
                    'rule' => json_encode($validationRule),
                    'value' => json_encode($ruleValue),
                    'type' => $valueType,
                    'correct_types' => implode("|", $correctTypesAndClasses)
                ]
        );

        return parent::__construct($message);
    }

    public static function ifRuleIsArrayValidateCorrectTypeOfItsValues(
            string|Rule|array $validationRule
    ): void {
        if (!is_array($validationRule)) {
            return;
        }
        foreach ($validationRule as $value) {
            self::validateValidationRuleValueType($validationRule,
                    $value, ['string']);
        }
    }

    public static function validateValidationRuleValueType(
            mixed $validationRule,
            mixed $ruleValue,
            array $correctTypes
    ): void {
        $valueType = gettype($ruleValue);

        if (in_array($valueType, $correctTypes)) {
            return;
        }

        if (!is_object($ruleValue)) {
            $correctTypesAndClasses = array_merge($correctTypes,
                    self::SUPPORTED_RULE_CLASSES);
            throw new self($validationRule, $ruleValue, $valueType,
                            $correctTypesAndClasses);
        }

        IncorrectObjectRuleException::validateRuleIsAnInstenseOfASupportedClass(
                $ruleValue);
    }

    public static function validateKeyTwoValueType(
            mixed $ruleValue, array $correctTypes
    ) {
        self::validateValidationRuleValueType($ruleValue, $ruleValue, 
                $correctTypes);
    }
}
