<?php
namespace Zodream\Validate;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/12/6
 * Time: 12:16
 */
use Zodream\Infrastructure\Support\MessageBag;
use Exception;
use Zodream\Validate\Rules\UrlRule;
use Zodream\Validate\Rules\RequiredRule;
use Zodream\Validate\Rules\RegexRule;
use Zodream\Validate\Rules\InRule;
use Zodream\Validate\Rules\MaxRule;
use Zodream\Validate\Rules\MinRule;
use Zodream\Validate\Rules\NumericRule;
use Zodream\Validate\Rules\EmailRule;
use Zodream\Validate\Rules\PhoneRule;

/**
 * Class Validator
 * @package Zodream\Validate
 * @method static UrlRule url()
 * @method static RequiredRule required()
 * @method static RegexRule regex(string $regex)
 * @method static Validator age(int $minAge = null, int $maxAge = null)
 * @method static InRule in(mixed $haystack, bool $compareIdentical = false)
 * @method static MaxRule max(mixed $maxValue, bool $inclusive = true)
 * @method static MinRule min(mixed $minValue, bool $inclusive = true)
 * @method static NumericRule numeric()
 * @method static Validator bool()
 * @method static EmailRule email()
 * @method static Validator equals(mixed $compareTo)
 * @method static Validator length(int $min = null, int $max = null, bool $inclusive = true)
 * @method static Validator size(string $minSize = null, string $maxSize = null)
 * @method static Validator unique()
 * @method static PhoneRule phone()
 */
class Validator {

    protected $attributes = [];
    protected $rules = [];
    /**
     * @var MessageBag
     */
    protected $message;

    public function setAttributes($arg) {
        $this->attributes = (array)$arg;
        return $this;
    }

    public function setRules($args) {
        foreach ((array)$args as $key => $arg) {
            $this->rules[] = $this->converterRule($arg, $key);
        }
        return $this;
    }

    protected function converterRule($rule, $key = null) {

    }

    /**
     * Determine if the data passes the validation rules.
     *
     * @return bool
     */
    public function passes() {
        $this->message = new MessageBag;

        // We'll spin through each rule, validating the attributes attached to that
        // rule. Any error messages will be added to the containers with each of
        // the other error messages, returning true if we don't have messages.
        foreach ($this->rules as $attribute => $rules) {

        }

        return $this->message->isEmpty();
    }

    /**
     * Determine if the data fails the validation rules.
     *
     * @return bool
     */
    public function fails() {
        return ! $this->passes();
    }

    /**
     * Run the validator's rules against its data.
     *
     * @param array $data
     * @return boolean
     */
    public function validate(array $data = []) {
        if (!empty($data)) {
            $this->setAttributes($data);
        }
        return !$this->fails();
    }

    /**
     * Get the message container for the validator.
     *
     * @return MessageBag
     */
    public function messages() {
        if (! $this->message) {
            $this->passes();
        }
        return $this->message;
    }

    public function errors() {
        return $this->messages()->all();
    }

    public function firstError() {
        return $this->messages()->first();
    }


    public static function make(array $rules, array $data = null) {
        $validator = new static();
        $validator->setRules($rules);
        if (is_null($data)) {
            return $validator;
        }
        return $validator->validate($data);
    }

    /**
     * @param $ruleName
     * @param array $arguments
     * @return RuleInterface
     * @throws Exception
     */
    public static function buildRule($ruleName, $arguments = []) {
        return RuleFactory::getInstance()->rule($ruleName, $arguments);
    }

    public static function __callStatic($ruleName, $arguments) {
        return static::buildRule($ruleName, $arguments);
    }

}