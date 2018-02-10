<?php
namespace Zodream\Validate\Rules;


class BoolRule extends AbstractRule {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate($input) {
        return is_bool($input) || $input == 0 || $input == 1;
    }
}