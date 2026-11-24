<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class TimestampRule extends AbstractRule {

    public readonly int|null $minValue;
    public readonly int|null $maxValue;

    public function __construct(
        int|float|string|null $min = null,
        int|float|string|null $max = null) {
        $this->minValue = is_null($min) ? $min : $this->filter($min);
        $this->maxValue = is_null($max) ? $max : $this->filter($max);
    }

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate(mixed $input): bool {
        if (is_int($input) || is_float($input)) {
            return $this->validateMax($input) && $this->validateMin($input);
        }
        $res = strtotime($input);
        if ($res === false) {
            return false;
        }
        return $this->validateMax($res) && $this->validateMin($res);
    }

    protected function validateMax(mixed $input): bool {
        return is_null($this->maxValue) || $this->maxValue > $input;
    }

    protected function validateMin(mixed $input): bool {
        return is_null($this->minValue) || $this->minValue <= $input;
    }

    public function filter(mixed $input): mixed {
        if (is_int($input) || is_float($input)) {
            return intval($input);
        }
        if (is_numeric($input)) {
            return strlen($input) > 10 ? intval($input) / 1000 : intval($input); 
        }
        return strtotime($input);
    }

}