<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class MinRule extends AbstractRule {

    public $minValue = 0;

    public function __construct($min = 0) {
        $this->minValue = $min;
    }

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate($input): bool {
        return $input >= $this->minValue;
    }
}