<?php
namespace Zodream\Validate\Rules;


class EmailRule extends AbstractRule {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate($input) {
        return is_string($input) && filter_var($input, FILTER_VALIDATE_EMAIL);
    }
}