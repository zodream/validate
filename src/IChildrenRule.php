<?php
declare(strict_types=1);
namespace Zodream\Validate;


interface IChildrenRule {

    public function validateNext(mixed $input, string $key, Validator $instance): bool;

    public function filterNext(array $input, Validator $instance): array;
}