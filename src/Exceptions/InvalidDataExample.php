<?php

namespace Arielenter\Validation\Exceptions;

use Arielenter\Validation\Constants\TransPrefix;
use ValueError;
use function __;
use function json_encode;

class InvalidDataExample extends ValueError {
    
    use TransPrefix;

    public function __construct(
            array $invalidDataExample,
            array $fieldValidationRule
    ) {
        $message = __(
                $this::TRANS_PREFIX . 'not_invalid_data',
                [
                    'data' => json_encode($invalidDataExample),
                    'rule' => json_encode($fieldValidationRule)
                ]
        );

        return parent::__construct($message);
    }

    public static function validate(
            string $expectedErrorMessage,
            array $invalidDataExample,
            array $fieldValidationRule,
    ): void {
        if (empty($expectedErrorMessage)) {
            throw new self($invalidDataExample, $fieldValidationRule);
        }
    }
}
