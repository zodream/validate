<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class IntRule extends AbstractRule {

    public function __construct(
        protected int|float|null $min = null,
        protected int|float|null $max = null) {
    }

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate(mixed $input): bool {
        if (is_bool($input)) {
            $input = $input ? 1 : 0;
        }
        if (is_integer($input)) {
            return $this->validateMax($input) && $this->validateMin($input);
        }
        return $input == intval($input) && $this->validateMax($input) && $this->validateMin($input);
    }

    protected function validateMax(mixed $input): bool {
        return is_null($this->max) || $this->max > $input;
    }

    protected function validateMin(mixed $input): bool {
        return is_null($this->min) || $this->min <= $input;
    }
}