<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class MinRule extends AbstractRule {

    protected int|float $minValue = 0;

    public function __construct(mixed $min = 0) {
        $this->minValue = !is_float($min) && !is_integer($min) ? floatval($min) : $min;
    }

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate(mixed $input): bool {
        return $input >= $this->minValue;
    }
}