<?php

namespace Arielenter\Validation\Exceptions;

use Arielenter\Validation\Constants\SupportedRequestMethods;
use Arielenter\Validation\Constants\TransPrefix;
use Illuminate\Support\Str;
use ValueError;
use function __;

class UnsupportedRequestMethodException extends ValueError {

    use TransPrefix,
        SupportedRequestMethods;

    public function __construct(string $requestMethod) {
        $message = __(
                $this::TRANS_PREFIX . 'unsupported_request_method',
                [
                    'method' => $requestMethod,
                    'supported_methods' => json_encode($this::SUPPORTED_METHODS)
                ]
        );

        return parent::__construct($message);
    }

    public static function validateRequestMethod(string $requestMethod)
    : string {
        $method = Str::of($requestMethod)->camel()->lower()->replace('j', 'J')
                ->toString() ;

        if (in_array($method, self::SUPPORTED_METHODS)) {
            return $method;
        }

        throw new self($requestMethod);
    }
}
