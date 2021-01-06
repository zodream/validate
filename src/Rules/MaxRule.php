<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class MaxRule extends AbstractRule {

    public $maxValue = 255;

    public function __construct($max = 255) {
        $this->maxValue = $max;
    }
    /**
     * 验证信息
     * @param string $input
     * @return boolean
     */
    public function validate($input): bool {
        return $input <= $this->maxValue;
    }
}