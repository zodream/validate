<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;

abstract class AbstractRegexRule extends AbstractFilterRule {

    abstract protected function getPregFormat(): string;

    public function validateClean(mixed $input): bool {
        return preg_match($this->getPregFormat(), $input);
    }
}