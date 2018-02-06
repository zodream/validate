<?php
namespace Zodream\Validate\Rules;


class RegexRule extends AbstractRule {

    public $regex;

    public function __construct($regex) {
        $this->regex = $regex;
    }

    public function validate($input) {
        if (!is_scalar($input)) {
            return false;
        }

        return (bool) preg_match($this->regex, (string) $input);
    }
}