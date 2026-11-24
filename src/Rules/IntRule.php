<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class IntRule extends AbstractRule {

    public readonly int|null $minValue;
    public readonly int|null $maxValue;

    public function __construct(
        int|float|string|null $min = null,
        int|float|string|null $max = null) {
        $this->minValue = is_null($min) ? $min : intval($min);
        $this->maxValue = is_null($max) ? $max : intval($max);
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
        if (is_int($input)) {
            return $this->validateMax($input) && $this->validateMin($input);
        }
        return $input == intval($input) && $this->validateMax($input) && $this->validateMin($input);
    }

    protected function validateMax(mixed $input): bool {
        return is_null($this->maxValue) || $this->maxValue > $input;
    }

    protected function validateMin(mixed $input): bool {
        return is_null($this->minValue) || $this->minValue <= $input;
    }

    public function filter(mixed $input): mixed {
        return intval($input);
    }
}