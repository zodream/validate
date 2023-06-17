<?php
declare(strict_types=1);
namespace Zodream\Validate;

use Exception;

interface RuleInterface {

    public function assert(mixed $input): void;

    public function check(mixed $input): void;

    public function reportError(mixed $input, array $relatedExceptions = []): Exception;

    public function validate(mixed $input): bool;
}