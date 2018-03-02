<?php
namespace Zodream\Validate\Rules;


class NullableRule extends AbstractRule {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate($input) {
        return true;
    }
}