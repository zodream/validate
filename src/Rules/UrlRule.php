<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class UrlRule extends AbstractRule {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate(mixed $input): bool {
        return filter_var($input, FILTER_VALIDATE_URL) !== false;
    }
}