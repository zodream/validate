<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;

use Closure;

class RequiredIfRule extends AbstractRule {

    public function __construct(
        private readonly bool|Closure $condition
    ) {
    }

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate(mixed $input): bool {
        $check = is_bool($this->condition) ? $this->condition : call_user_func($this->condition);
        if (!$check) {
            return true;
        }
        return !RequiredRule::isEmpty($input);
    }
}