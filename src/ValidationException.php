<?php
declare(strict_types=1);
namespace Zodream\Validate;

use Exception;

class ValidationException extends Exception {
    /**
     * The validator instance.
     *
     * @var Validator
     */
    public $validator;

    /**
     * Create a new exception instance.
     *
     * @param  Validator  $validator
     */
    public function __construct($validator) {
        parent::__construct(
            __('The given data failed to pass validation.')
        );
        $this->validator = $validator;
    }
}
