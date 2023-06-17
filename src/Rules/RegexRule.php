<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class RegexRule extends AbstractRule {

    public function __construct(
        protected string $regex) {
    }

    public function validate(mixed $input): bool {
        if (!is_scalar($input)) {
            return false;
        }
        return (bool) preg_match($this->regex, (string) $input);
    }
}