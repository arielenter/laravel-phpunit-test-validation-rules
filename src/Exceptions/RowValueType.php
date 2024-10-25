<?php

namespace Arielenter\Validation\Exceptions;

use Arielenter\Validation\Constants\AssertionsTrans;
use TypeError;
use function __;
use function json_encode;

class RowValueType extends TypeError {

    use AssertionsTrans;

    public function __construct(mixed $currentRow, string|int $currentRowKey) {
        $message = __(
                $this::ASSERTIONS_ERRORS_TRANS . 'row_should_had_been_a_nested_'
                . 'array',
                [
                    'key' => $currentRowKey,
                    'value' => json_encode($currentRow),
                    'type' => gettype($currentRow)
                ]
        );

        return parent::__construct($message);
    }

    public static function validate(
            mixed $currentRow,
            string|int $currentRowKey
    ) {
        if (is_array($currentRow)) {
            return;
        }

        throw new self($currentRow, $currentRowKey);
    }
}
