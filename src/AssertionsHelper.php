<?php

declare(strict_types=1);

namespace arielenter\Validation;

use ArgumentCountError;
use BadMethodCallException;
use Exception;
use Illuminate\Contracts\Validation\Rule;
use TypeError;
use ValueError;
use function __;
use function validator;

trait AssertionsHelper {

    private mixed $currentRow;
    private mixed $currentRowOriginalValue;
    private string|int $currentRowKey;
    private string $transKeyPrefix = 'ValidationAssertions::errors';

    private function validateRequestMethod(string $requestMethod): string {
        $requestMethodLowerCase = strtolower($requestMethod);
        $supportedMethods = ['get', 'post', 'put', 'patch', 'delete',
            'options'];

        if (in_array($requestMethodLowerCase, $supportedMethods)) {
            return $requestMethodLowerCase;
        }

        $thansKey = "{$this->transKeyPrefix}.unsupported_request_method";

        $replace = [
            'method' => $requestMethod,
            'supported_methods' => json_encode($supportedMethods)
        ];

        throw new ValueError(__($thansKey, $replace));
    }

    private function validateValidationRuleValueTypes(
            string|Rule|array $validationRule
    ): void {
        if (!is_array($validationRule)) {
            return;
        }
        foreach ($validationRule as $value) {
            $this->validateValidationRuleValueType($validationRule,
                    $value, ['string'], [Rule::class]);
        }
    }

    private function validateValidationRuleValueType(
            mixed $validationRule,
            mixed $value,
            array $correctTypes,
            array $correctClasses
    ): void {
        $valueType = gettype($value);

        if (in_array($valueType, $correctTypes)) {
            return;
        }

        if (!is_object($value)) {
            $correctTypesAndClasses = array_merge($correctTypes,
                    $correctClasses);
            $this->throwRuleTypeError($validationRule, $value,
                    $correctTypesAndClasses, $valueType);
        }

        foreach ($correctClasses as $class) {
            if ($value instanceof $class) {
                return;
            }
        }

        $transKey = "{$this->transKeyPrefix}.incorrect_object_rule";
        $replace = ['rule' => get_class($value),
            'classes' => implode("|", $correctClasses)];

        throw new TypeError(__($transKey, $replace));
    }

    private function throwRuleTypeError(
            mixed $validationRule,
            mixed $value,
            array $correctTypesAndClasses,
            string $valueType
    ): void {
        $transKey = "{$this->transKeyPrefix}.incorrect_rule_value_type";

        $replace = [
            'rule' => json_encode($validationRule),
            'value' => json_encode($value),
            'type' => $valueType,
            'correct_types' => implode("|", $correctTypesAndClasses)
        ];

        throw new TypeError(__($transKey, $replace));
    }

    private function getExpectedErrorMessage(
            array $invalidDataExample,
            array $fieldValidationRule,
            string|Rule|array $validationRule
    ): string {
        try {
            $expectedErrorMessage = validator($invalidDataExample,
                            $fieldValidationRule)->messages()->first();
        } catch (BadMethodCallException $e) {
            $transKey = "{$this->transKeyPrefix}.incorrect_rule_value";
            $replace = [
                'rule' => json_encode($validationRule),
                'validator_error' => $e->getMessage()
            ];

            throw new ValueError(__($transKey, $replace));
        }

        if ($expectedErrorMessage != '') {
            return $expectedErrorMessage;
        }

        throw new Exception(__("{$this->transKeyPrefix}.not_invalid_data", [
                            'data' => json_encode($invalidDataExample),
                            'rule' => json_encode($fieldValidationRule)
        ]));
    }

    private function validateRowArrayShape(): void {
        $this->validateCurrentRowIsArray();

        $this->validateCurrentRowHasIntKeysCeroOneAndTwo();

        $this->validateKeyTwoValueType();

        $this->turnValueOfKeysCeroAndOneOfCurrentRowIntoArrayIfItIsntOneAlrdy();

        foreach ($this->currentRow[0] as $fieldKey => $fieldName) {
            $this->validateFieldNameIsString($fieldKey, $fieldName);
        }
    }

    private function validateCurrentRowIsArray(): void {
        $currentRow = $this->currentRow;
        if (is_array($currentRow)) {
            return;
        }

        $transKey = "{$this->transKeyPrefix}.row_should_had_been_a_nested_"
                . "array";

        $replace = [
            'key' => $this->currentRowKey,
            'value' => json_encode($currentRow),
            'type' => gettype($currentRow)
        ];

        throw new TypeError(__($transKey, $replace));
    }

    private function
    validateCurrentRowHasIntKeysCeroOneAndTwo(): void {
        $currentRow = $this->currentRow;
        for ($key = 0; $key <= 2; $key++) {
            if (array_key_exists($key, $currentRow)) {
                continue;
            }

            $transKey = "{$this->transKeyPrefix}.row_has_a_missing_key";

            $replace = [
                'row_key' => $this->currentRowKey,
                'value' => json_encode($currentRow),
                'missing_key' => $key
            ];

            throw new ArgumentCountError(__($transKey, $replace));
        }
    }

    private function
    turnValueOfKeysCeroAndOneOfCurrentRowIntoArrayIfItIsntOneAlrdy(): void {
        $this->currentRowOriginalValue = $this->currentRow;
        foreach ([0, 1] as $key) {
            if (is_array($this->currentRow[$key])) {
                continue;
            }
            $this->currentRow[$key] = [$this->currentRow[$key]];
        }
    }

    private function validateKeyTwoValueType(): void {
        $this->validateValidationRuleValueType(
                validationRule: $this->currentRow[2],
                value: $this->currentRow[2],
                correctTypes: ['string', 'array'],
                correctClasses: [Rule::class]
        );
    }

    private function
    validateFieldNameIsString(string|int $fieldKey, mixed $fieldName): void {
        if (is_string($fieldName)) {
            return;
        }

        $transKey = "{$this->transKeyPrefix}.wrong_field_name_value_type";

        $replace = [
            'row_key' => $this->currentRowKey,
            'row_value' => json_encode($this->currentRowOriginalValue),
            'field_key' => $fieldKey,
            'field_name' => json_encode($fieldName),
            'actual_type' => gettype($fieldName)
        ];

        throw new TypeError(__($transKey, $replace));
    }

    private function pairFieldsWithValues(): array {
        $fieldValuePairs = [];
        foreach ($this->currentRow[0] as $field) {
            foreach ($this->currentRow[1] as $value) {
                $fieldValuePairs[] = [$field, $value];
            }
        }
        return $fieldValuePairs;
    }
}
