<?php
namespace Zodream\Validate\Rules;


class TimeRule extends AbstractRule {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate($input) {
        return strtotime($input) !== false;
    }
}