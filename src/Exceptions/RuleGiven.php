<?php

namespace Arielenter\Validation\Exceptions;

use Arielenter\Validation\Constants\TransPrefix;
use BadMethodCallException;
use Illuminate\Contracts\Validation\Rule;
use ValueError;
use function __;
use function validator;
use function json_encode;

class RuleGiven extends ValueError {
    
    use TransPrefix;

    public function __construct(
            string|array|Rule $validationRule,
            string $validatorError
    ) {
        $message = __(
                $this::TRANS_PREFIX . 'unknown_rule_given',
                [
                    'rule' => json_encode($validationRule),
                    'validator_error' => $validatorError
                ]
        );

        return parent::__construct($message);
    }

    public static function tryGetValidationErrorMessage(
            array $invalidDataExample,
            array $fieldValidationRule,
            string|Rule|array $validationRule
    ): string {
        try {
            $expectedErrorMessage = validator($invalidDataExample,
                            $fieldValidationRule)->messages()->first();
        } catch (BadMethodCallException $e) {
            throw new self($validationRule, $e->getMessage());
        }

        InvalidDataExample::validate($expectedErrorMessage, $invalidDataExample,
                $fieldValidationRule);

        return $expectedErrorMessage;
    }
}
