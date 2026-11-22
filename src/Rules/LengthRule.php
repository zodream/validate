<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class LengthRule extends AbstractRule {

    public readonly int|null $minValue;
    public readonly int|null $maxValue;

    public function __construct(
        int|string|null $min = null, 
        int|string|null $max = null, 
        public readonly bool $inclusive = true) {
        $this->minValue = is_null($min) ? $min : intval($min);
        $this->maxValue = is_null($max) ? $max : intval($max);
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