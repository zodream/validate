<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class IntRule extends AbstractRule {

    protected $min;

    protected $max;

    public function __construct($min = null, $max = null) {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate($input): bool {
        if (is_integer($input)) {
            return $this->validateMax($input) && $this->validateMin($input);
        }
        return $input == intval($input) && $this->validateMax($input) && $this->validateMin($input);
    }

    protected function validateMax($input) {
        return is_null($this->max) || $this->max > $input;
    }

    protected function validateMin($input) {
        return is_null($this->min) || $this->min <= $input;
    }
}