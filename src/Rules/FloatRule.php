<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class FloatRule extends AbstractRule {

    public readonly float|null $minValue;
    public readonly float|null $maxValue;

    public function __construct(
        int|float|string|null $min = null,
        int|float|string|null $max = null) {
        $this->minValue = is_null($min) ? $min : floatval($min);
        $this->maxValue = is_null($max) ? $max : floatval($max);
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
        if (is_numeric($input)) {
            return $this->validateMax($input) && $this->validateMin($input);
        }
        return $input == floatval($input) && $this->validateMax($input) && $this->validateMin($input);
    }

    protected function validateMax(mixed $input): bool {
        return is_null($this->maxValue) || $this->maxValue > $input;
    }

    protected function validateMin(mixed $input): bool {
        return is_null($this->minValue) || $this->minValue <= $input;
    }

    public function filter(mixed $input): mixed {
        return floatval($input);
    }
}