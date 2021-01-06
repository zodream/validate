<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class NumericRule extends AbstractRule {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate($input): bool {
        return is_numeric($input);
    }
}