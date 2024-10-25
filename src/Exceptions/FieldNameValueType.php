<?php

namespace Arielenter\Validation\Exceptions;

use Arielenter\Validation\Constants\AssertionsTrans;
use TypeError;
use function __;
use function json_encode;

class FieldNameValueType extends TypeError {

    use AssertionsTrans;

    public function __construct(
            string|int $fieldKey,
            mixed $fieldName,
            string|int $currentRowKey,
            array $currentRow
    ) {
        $message = __(
                $this::ASSERTIONS_ERRORS_TRANS . 'wrong_field_name_value_type',
                [
                    'field_key' => $fieldKey,
                    'field_name' => json_encode($fieldName),
                    'row_key' => $currentRowKey,
                    'row_value' => json_encode($currentRow),
                    'actual_type' => gettype($fieldName)
                ]
        );

        return parent::__construct($message);
    }

    public static function validate(
            string|int $fieldKey,
            mixed $fieldName,
            string|int $currentRowKey,
            array $currentRow
    ): void {
        if (is_string($fieldName)) {
            return;
        }

        throw new self($fieldKey, $fieldName, $currentRowKey, $currentRow);
    }
}
