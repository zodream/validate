<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class LengthRule extends AbstractRule {

    public $minValue;
    public $maxValue;
    public $inclusive;

    public function __construct($min = null, $max = null, $inclusive = true) {
        $this->minValue = $min;
        $this->maxValue = $max;
        $this->inclusive = $inclusive;
    }

    public function validate(mixed $input): bool {
        $length = $this->extractLength($input);

        return $this->validateMin($length) && $this->validateMax($length);
    }

    protected function extractLength(mixed $input): int {
        if (is_string($input)) {
            $encoding = mb_detect_encoding($input);
            return $encoding ? mb_strlen($input, $encoding) : mb_strlen($input);
        }

        if (is_array($input) || $input instanceof \Countable) {
            return count($input);
        }

        if (is_object($input)) {
            return count(get_object_vars($input));
        }

        if (is_int($input)) {
            return mb_strlen((string) $input);
        }

        return 0;
    }

    protected function validateMin(int $length): bool {
        if (is_null($this->minValue)) {
            return true;
        }

        if ($this->inclusive) {
            return $length >= $this->minValue;
        }

        return $length > $this->minValue;
    }

    protected function validateMax(int $length): bool {
        if (is_null($this->maxValue)) {
            return true;
        }

        if ($this->inclusive) {
            return $length <= $this->maxValue;
        }

        return $length < $this->maxValue;
    }
}