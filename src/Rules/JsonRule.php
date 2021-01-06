<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class JsonRule extends AbstractRule {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate($input): bool {
        if (!is_string($input) || '' === $input) {
            return false;
        }
        json_decode($input);
        return JSON_ERROR_NONE === json_last_error();
    }
}