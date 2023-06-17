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
    private array $rulesNamespaces = [];

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
}