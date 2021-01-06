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
    abstract public function validate($input): bool;

    /**
     * 验证
     * @param mixed $input
     * @throws Exception
     */
    public function assert($input) {
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
    public function check($input){
        $this->assert($input);
    }

    /**
     * @param mixed $input
     * @param array $extraParams
     * @return Exception
     */
    public function reportError($input, array $extraParams = []) {
        return new Exception(sprintf(
            __('%s is error'),
            $input));
    }


    public function __invoke($input) {
        return $this->validate($input);
    }
}