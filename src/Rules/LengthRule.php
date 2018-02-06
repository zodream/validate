<?php
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

    public function validate($input) {
        $length = $this->extractLength($input);

        return $this->validateMin($length) && $this->validateMax($length);
    }

    protected function extractLength($input) {
        if (is_string($input)) {
            return mb_strlen($input, mb_detect_encoding($input));
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

        return false;
    }

    protected function validateMin($length) {
        if (is_null($this->minValue)) {
            return true;
        }

        if ($this->inclusive) {
            return $length >= $this->minValue;
        }

        return $length > $this->minValue;
    }

    protected function validateMax($length) {
        if (is_null($this->maxValue)) {
            return true;
        }

        if ($this->inclusive) {
            return $length <= $this->maxValue;
        }

        return $length < $this->maxValue;
    }
}