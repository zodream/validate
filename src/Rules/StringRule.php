<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class StringRule extends LengthRule {


    public function filter(mixed $input): mixed {
        return (string)$input;
    }
}