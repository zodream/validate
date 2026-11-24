<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class DatetimeRule extends AbstractRule {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate(mixed $input): bool {
        return is_string($input) &&strtotime($input) !== false;
    }
}