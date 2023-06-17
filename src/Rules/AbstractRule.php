<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;

use Exception;
use Zodream\Validate\RuleInterface;

abstract class AbstractRule implements RuleInterface {

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    abstract public function validate(mixed $input): bool;

    /**
     * 验证
     * @param mixed $input
     * @throws Exception
     */
    public function assert(mixed $input): void {
        if ($this->validate($input)) {
            return;
        }
        throw $this->reportError($input);
    }

    /**
     * 验证
     * @param mixed $input
     * @throws Exception
     */
    public function check(mixed $input): void {
        $this->assert($input);
    }

    /**
     * @param mixed $input
     * @param array $relatedExceptions
     * @return Exception
     * @throws Exception
     */
    public function reportError(mixed $input, array $relatedExceptions = []): Exception {
        return new Exception(sprintf(
            __('%s is error'),
            $input));
    }


    public function __invoke(mixed $input): bool {
        return $this->validate($input);
    }
}