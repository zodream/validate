<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class TimeRule extends AbstractRule {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate(mixed $input): bool {
        return strtotime($input) !== false;
    }
}