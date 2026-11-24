<?php
declare(strict_types=1);
namespace Zodream\Validate\Rules;


class ContainsRule extends AbstractRule {

    public function __construct(
        private readonly string $word
    )
    {
        
    }
    /**
     * 验证信息
     * @param mixed $input
     * @return boolean
     */
    public function validate(mixed $input): bool {
        return is_string($input) && str_contains($input, $this->word);
    }
}