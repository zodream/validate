<?php
declare(strict_types=1);
namespace Zodream\Validate;

use Zodream\Helpers\Str;
use ReflectionClass;
use Exception;
use Zodream\Infrastructure\Concerns\SingletonPattern;
use Zodream\Validate\Rules\NullableRule;

class RuleFactory {

    use SingletonPattern;

    const DEFAULT_RULES_NAMESPACES = [
        'Zodream\\Validate\\Rules',
    ];

    /**
     * @var string[]
     */
    private readonly array $rulesNamespaces;

    public function __construct(array $rulesNamespaces) {
        $this->rulesNamespaces = $this->filterNamespaces($rulesNamespaces, self::DEFAULT_RULES_NAMESPACES);
    }

    /**
     * @param string $ruleName
     * @param array $arguments
     * @return RuleInterface
     * @throws Exception
     */
    public function rule(string $ruleName, array $arguments = []): RuleInterface {
        if (empty($ruleName)) {
            return new NullableRule();
        }
        foreach ($this->rulesNamespaces as $namespace) {
            $className = sprintf('%s\\%sRule', $namespace, Str::studly($ruleName));
            if (!class_exists($className)) {
                continue;
            }
            return $this->createReflectionClass($className, RuleInterface::class)->newInstanceArgs($arguments);
        }
        throw new Exception(sprintf(
            __('"%s" is not a valid rule name'), $ruleName));
    }


    private function createReflectionClass(string $name, string $parentName): ReflectionClass {
        $reflection = new ReflectionClass($name);
        if (!$reflection->isSubclassOf($parentName)) {
            throw new Exception(sprintf(
                __('"%s" must be an instance of "%s"'),
                $name, $parentName));
        }

        if (!$reflection->isInstantiable()) {
            throw new Exception(sprintf(
                __('"%s" must be instantiable'), $name));
        }

        return $reflection;
    }

    private function filterNamespaces(array $namespaces, array $defaultNamespaces): array {
        $filter = function ($namespace){
            return trim($namespace, '\\');
        };

        return array_unique(
            array_merge(
                array_map($filter, $namespaces),
                array_map($filter, $defaultNamespaces)
            )
        );
    }

    /**
     * 根据类名获取规则名
     */
    public static function ruleName(RuleInterface|string $rule) {
        $cls = $rule instanceof RuleInterface ? get_class($rule) : $rule;
        $i = stripos($cls, '\\');
        $name = $i === false ? $cls : substr($cls, $i + 1);
        if (str_ends_with($name, 'Rule')) {
            $name = substr($name, 0, strlen($name) - 4);
        }
        return Str::unStudly($name);
    }
    /**
     * 转换
     * 
     */
    public static function convertFrom(array|string $rules, object|null $instance = null): array {
        if (is_string($rules)) {
            return self::convertRule($rules, $instance);
        }
        $res = [];
        foreach($rules as $keys => $rule) {
            if (is_int($keys) && is_array($rule)) {
                $keys = array_shift($rule);
            }
            $next = self::convertRule($rule, $instance);
            $next['keys'] = $keys;
            $res[] = $next;
        }
        return $res;
    }

    private static function convertRule(null|string|array|RuleInterface $rule, object|null $instance = null): array {
        if ($rule instanceof RuleInterface) {
            return [
                'rules' => [RuleFactory::ruleName($rule) => $rule], 
                'message' => null
            ];
        }
        if (!is_array($rule)) {
            $rule = empty($rule) ? [] : explode('|', $rule);
        }
        $rules = [];
        $message = null;
        $factory = self::getInstance();
        foreach ($rule as $key => $item) {
            if ($key === 'message') {
                $message = $item;
                continue;
            }
            if (!is_int($key)) {
                $rules[$key] = $factory->rule($key, (array)$item);
                continue;
            }
            if ($item instanceof RuleInterface) {
                $rules[RuleFactory::ruleName($item)] = $item;
                continue;
            }
            if (is_callable($item)) {
                $rules[] = $item;
                continue;
            }
            if (is_string($item) && !empty($instance) 
                && str_contains($item, ':') && method_exists($instance, $item)) {
                // 增加一个宿主调用方法
                $rules[] = [$instance, $rule];
                continue;
            }
            if (!is_string($item) || !str_contains($item, ':')) {
                $rules[$item] = $factory->rule($item, []);
                continue;
            }
            list($key, $val) = explode(':', $item, 2);
            if ($key === 'message') {
                $message = $val;
                continue;
            }
            $rules[$key] = $factory->rule($key, explode(',', $val));
        }
        return compact('rules', 'message');
    } 


}