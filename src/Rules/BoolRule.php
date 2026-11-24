<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class BoolRule extends AbstractRule {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate(mixed $input): bool {
        return is_bool($input) || $input === 0 || $input === 1 || $input === '0' || $input === '1';
    }

    public function filter(mixed $input): mixed {
        if (is_bool($input)) {
            return $input;
        }
        if ($input === 0 || $input === '0') {
            return false;
        }
        return true;
    }
}