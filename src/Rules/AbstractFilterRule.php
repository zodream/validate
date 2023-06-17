<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;

use Exception;

abstract class AbstractFilterRule extends AbstractRule {


    abstract protected function validateClean(mixed $input);

    public function __construct(
        public string $additionalChars = '') {
    }

    protected function filter(mixed $input): mixed {
        return str_replace(str_split($this->additionalChars), '', (string)$input);
    }

    public function validate(mixed $input): bool {
        if (!is_scalar($input)) {
            return false;
        }

        $stringInput = (string) $input;
        if ('' === $stringInput) {
            return false;
        }
        $cleanInput = $this->filter($stringInput);
        return '' === $cleanInput || $this->validateClean($cleanInput);
    }
}