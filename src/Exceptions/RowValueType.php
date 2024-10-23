<?php

namespace Arielenter\Validation\Exceptions;

use Arielenter\Validation\Constants\TransPrefix;
use TypeError;
use function __;
use function json_encode;

class RowValueType extends TypeError {

    use TransPrefix;

    public function __construct(mixed $currentRow, string|int $currentRowKey) {
        $message = __(
                $this::TRANS_PREFIX . 'row_should_had_been_a_nested_array',
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
