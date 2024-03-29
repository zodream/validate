<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;

class InRule extends AbstractRule {
    public mixed $haystack = null;
    public bool $compareIdentical = false;

    public function __construct(mixed $haystack, mixed $compareIdentical = false) {
        if (func_num_args() > 1
            && !is_array($haystack)
            && !is_bool($compareIdentical)) {
            $this->haystack = func_get_args();
            return;
        }
        $this->haystack = $haystack;
        $this->compareIdentical = $compareIdentical;
    }

    protected function validateEquals(mixed $input): bool {
        if (is_array($this->haystack)) {
            return in_array($input, $this->haystack);
        }

        if (null === $input || '' === $input) {
            return $input == $this->haystack;
        }

        $inputString = (string) $input;

        return false !== mb_stripos($this->haystack, $inputString, 0, mb_detect_encoding($inputString));
    }

    protected function validateIdentical(mixed $input): bool {
        if (is_array($this->haystack)) {
            return in_array($input, $this->haystack, true);
        }

        if (null === $input || '' === $input) {
            return $input === $this->haystack;
        }

        $inputString = (string) $input;

        return false !== mb_strpos($this->haystack, $inputString, 0, mb_detect_encoding($inputString));
    }

    public function validate(mixed $input): bool {
        if ($this->compareIdentical) {
            return $this->validateIdentical($input);
        }

        return $this->validateEquals($input);
    }
}