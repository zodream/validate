<?php
namespace Zodream\Validate;

use Zodream\Helpers\Str;
use Zodream\Infrastructure\Traits\SingletonPattern;
use ReflectionClass;
use Exception;

class RuleFactory {
    use SingletonPattern;

    const DEFAULT_RULES_NAMESPACES = [
        'Zodream\\Validate\\Rules',
    ];

    /**
     * @var string[]
     */
    private $rulesNamespaces = [];

    public function __construct(array $rulesNamespaces) {
        $this->rulesNamespaces = $this->filterNamespaces($rulesNamespaces, self::DEFAULT_RULES_NAMESPACES);
    }

    /**
     * @param string $ruleName
     * @param array $arguments
     * @return RuleInterface
     * @throws Exception
     */
    public function rule($ruleName, array $arguments = []) {
        foreach ($this->rulesNamespaces as $namespace) {
            $className = sprintf('%s\\%sRule', $namespace, Str::studly($ruleName));
            if (!class_exists($className)) {
                continue;
            }
            return $this->createReflectionClass($className, RuleInterface::class)->newInstanceArgs($arguments);
        }

        throw new Exception(sprintf('"%s" is not a valid rule name', $ruleName));
    }


    private function createReflectionClass($name, $parentName) {
        $reflection = new ReflectionClass($name);
        if (!$reflection->isSubclassOf($parentName)) {
            throw new Exception(sprintf('"%s" must be an instance of "%s"', $name, $parentName));
        }

        if (!$reflection->isInstantiable()) {
            throw new InvalidClassException(sprintf('"%s" must be instantiable', $name));
        }

        return $reflection;
    }

    private function filterNamespaces(array $namespaces, array $defaultNamespaces) {
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