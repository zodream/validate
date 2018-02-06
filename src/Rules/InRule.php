<?php
namespace Zodream\Validate\Rules;

class InRule extends AbstractRule {
    public $haystack;
    public $compareIdentical;

    public function __construct($haystack, $compareIdentical = false) {
        $this->haystack = $haystack;
        $this->compareIdentical = $compareIdentical;
    }

    protected function validateEquals($input) {
        if (is_array($this->haystack)) {
            return in_array($input, $this->haystack);
        }

        if (null === $input || '' === $input) {
            return $input == $this->haystack;
        }

        $inputString = (string) $input;

        return false !== mb_stripos($this->haystack, $inputString, 0, mb_detect_encoding($inputString));
    }

    protected function validateIdentical($input) {
        if (is_array($this->haystack)) {
            return in_array($input, $this->haystack, true);
        }

        if (null === $input || '' === $input) {
            return $input === $this->haystack;
        }

        $inputString = (string) $input;

        return false !== mb_strpos($this->haystack, $inputString, 0, mb_detect_encoding($inputString));
    }

    public function validate($input) {
        if ($this->compareIdentical) {
            return $this->validateIdentical($input);
        }

        return $this->validateEquals($input);
    }
}