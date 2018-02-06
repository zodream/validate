<?php
namespace Zodream\Validate\Rules;

abstract class AbstractRegexRule extends AbstractFilterRule {

    abstract protected function getPregFormat();

    public function validateClean($input) {
        return preg_match($this->getPregFormat(), $input);
    }
}