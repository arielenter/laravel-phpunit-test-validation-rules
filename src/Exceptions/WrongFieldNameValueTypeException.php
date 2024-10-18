<?php

namespace Arielenter\Validation\Exceptions;

use Arielenter\Validation\Constants\TransPrefix;
use TypeError;
use function __;
use function json_encode;

class WrongFieldNameValueTypeException extends TypeError {

    use TransPrefix;

    public function __construct(
            string|int $fieldKey,
            mixed $fieldName,
            string|int $currentRowKey,
            array $currentRow
    ) {
        $message = __(
                $this::TRANS_PREFIX . 'wrong_field_name_value_type',
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

    public static function validateFieldNameIsString(
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
