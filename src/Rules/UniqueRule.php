<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;

use Zodream\Database\Contracts\SqlBuilder;

class UniqueRule extends AbstractRule {

    public function __construct(
        private readonly SqlBuilder $query,
        private readonly string $column
    ) {
        
    }
    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate(mixed $input): bool {
        return !(clone $this->query)->where($this->column, '=', $input)->any();
    }
}