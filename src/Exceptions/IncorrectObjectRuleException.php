<?php

namespace Arielenter\Validation\Exceptions;

use Arielenter\Validation\Constants\SupportedRuleClasses;
use Arielenter\Validation\Constants\TransPrefix;
use TypeError;
use function __;

class IncorrectObjectRuleException extends TypeError {

    use TransPrefix,
        SupportedRuleClasses;

    public function __construct(
            object $ruleObject,
            array $correctClasses
    ) {
        $message = __(
                $this::TRANS_PREFIX . 'incorrect_object_rule',
                [
                    'rule' => get_class($ruleObject),
                    'classes' => implode("|", $correctClasses)
                ]
        );

        return parent::__construct($message);
    }

    public static function validateRuleIsAnInstenseOfASupportedClass(
            object $ruleObject
    ) {
        foreach (self::SUPPORTED_RULE_CLASSES as $class) {
            if ($ruleObject instanceof $class) {
                return;
            }
        }

        throw new self($ruleObject, self::SUPPORTED_RULE_CLASSES);
    }
}
