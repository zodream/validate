<?php
namespace Zodream\Validate\Rules;


class JsonRule extends AbstractRule {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate($input) {
        if (!is_string($input) || '' === $input) {
            return false;
        }
        json_decode($input);
        return JSON_ERROR_NONE === json_last_error();
    }
}