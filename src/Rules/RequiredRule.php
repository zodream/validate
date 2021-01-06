<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class RequiredRule extends AbstractRule {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate($input): bool {
        if (is_null($input)) {
            return false;
        }
        if (is_string($input)) {
            return trim($input) !== '';
        }
        if (!is_array($input)) {
            return true;
        }
        foreach ($input as $item) {
            if ($this->validate($item)) {
                return true;
            }
        }
        return false;
    }
}