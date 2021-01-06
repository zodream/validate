<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class RegexRule extends AbstractRule {

    public $regex;

    public function __construct($regex) {
        $this->regex = $regex;
    }

    public function validate($input): bool {
        if (!is_scalar($input)) {
            return false;
        }

        return (bool) preg_match($this->regex, (string) $input);
    }
}