<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;

use Zodream\Validate\IChildrenRule;
use Zodream\Validate\RuleFactory;
use Zodream\Validate\Validator;

class ArrayRule extends AbstractRule implements IChildrenRule {

    private readonly array $rules;
    private readonly bool $valueIsMap;

    public function __construct(array|string $rules) {
        $this->valueIsMap = is_array($rules);
        $this->rules = RuleFactory::convertFrom((array)$rules);
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
        return $this->validateNext($input, '', $instance);
    }

    public function filter(mixed $input): mixed {
        $instance = new Validator();
        return $this->filterNext($input, $instance);
    }

    public function validateNext(mixed $input, string $key, Validator $instance): bool {
        if (!is_array($input)) {
            return false;
        }
        if ($key !== '') {
            $key .= '.';
        }
        $res = true;
        if (!$this->valueIsMap) {
            $rules = current($this->rules);
            foreach($input as $i => $item) {
                if (!$instance->internalValidateRule($key.$i, $item, $rules['rules'], $rules['message'])) {
                    $res = false;
                }
            }
        } else {
            foreach($input as $i => $item) {
                if (!$instance->internalValidate($this->rules, (array)$item, $key.$i.'.')) {
                    $res = false;
                }
            }
        }
        return $res;
    }

    public function filterNext(array $input, Validator $instance): array {
        $res = [];
        if (!$this->valueIsMap) {
            $rules = current($this->rules);
            foreach($input as $item) {
                $res[] = $instance->internalFilterRule($rules['rules'], $item);
            }
        } else {
            foreach($input as $item) {
                $res[] = $instance->internalFilter($this->rules, $item);
            }
        }
        return $res;
    }
}