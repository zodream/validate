<?php
declare(strict_types=1);
namespace Zodream\Validate;

use Exception;
use Zodream\Infrastructure\Contracts\ArrayAble;
use Zodream\Infrastructure\Support\MessageBag;

class ValidationException extends Exception implements ArrayAble {

    public MessageBag $bag;

    /**
     * Create a new exception instance.
     *
     * @param Validator|MessageBag $validator
     */
    public function __construct(
        Validator|MessageBag $validator) {
        $this->bag = $validator instanceof Validator ? $validator->messages() : $validator;
        parent::__construct(
            $this->bag->first()
            // __('The given data failed to pass validation.')
        );
    }

    public function toArray(): array {
        return $this->bag->toArray();
    }
}
