<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class MaxRule extends AbstractRule {

    protected float|int $maxValue = 255;

    public function __construct(mixed $max = 255) {
        $this->maxValue = !is_float($max) && !is_integer($max) ? floatval($max) : $max;
    }
    /**
     * 验证信息
     * @param string $input
     * @return boolean
     */
    public function validate(mixed $input): bool {
        return $input <= $this->maxValue;
    }
}