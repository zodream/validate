<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class RequiredRule extends AbstractRule {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate(mixed $input): bool {
        return !self::isEmpty($input);
    }

    public static function isEmpty(mixed $input): bool {
        if (is_null($input)) {
            return true;
        }
        if (is_string($input)) {
            return trim($input) === '';
        }
        if (!is_array($input)) {
            return false;
        }
        foreach ($input as $item) {
            if (!self::isEmpty($item)) {
                return false;
            }
        }
        return true;
    }
}