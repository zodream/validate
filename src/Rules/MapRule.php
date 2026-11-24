<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;

use Zodream\Validate\IChildrenRule;
use Zodream\Validate\RuleFactory;
use Zodream\Validate\Validator;

class MapRule extends AbstractRule implements IChildrenRule {

    private readonly array $rules;

    public function __construct(array $rules) {
        $this->rules = RuleFactory::convertFrom($rules);
    }

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate(mixed $input): bool {
        if (!is_array($input)) {
            return false;
        }
        $instance = new Validator();
        return $instance->internalValidate($this->rules, $input);
    }

    public function filter(mixed $input): mixed {
        $instance = new Validator();
        return $instance->internalFilter($this->rules, $input);
    }

    public function validateNext(mixed $input, string $key, Validator $instance): bool {
        return is_array($input) && $instance->internalValidate($this->rules, $input, $key.'.');
    }

    public function filterNext(array $input, Validator $instance): array {
        return $instance->internalFilter($this->rules, $input);
    }
}