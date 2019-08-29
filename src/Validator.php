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
use Zodream\Service\Factory;
use Zodream\Validate\Rules\UrlRule;
use Zodream\Validate\Rules\RequiredRule;
use Zodream\Validate\Rules\RegexRule;
use Zodream\Validate\Rules\InRule;
use Zodream\Validate\Rules\MaxRule;
use Zodream\Validate\Rules\MinRule;
use Zodream\Validate\Rules\NumericRule;
use Zodream\Validate\Rules\EmailRule;
use Zodream\Validate\Rules\PhoneRule;
use Zodream\Validate\Rules\IntRule;

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
 * @method static IntRule int()
 * @method static Validator equals(mixed $compareTo)
 * @method static Validator length(int $min = null, int $max = null, bool $inclusive = true)
 * @method static Validator size(string $minSize = null, string $maxSize = null)
 * @method static Validator unique()
 * @method static PhoneRule phone()
 */
class Validator {

    protected $attributes = [];
    protected $rules = [];
    protected $labels = [];
    protected $messages = [
        'required' => ':attribute is required.',
        'int' => ':attribute is invalid integer.',
        'in' => ':attribute is not in range.',
        'numeric' => ':attribute is invalid numeric.',
        'phone' => ':attribute is invalid phone.',
        'string' => ':attribute is invalid string.',
        'length' => ':attribute is invalid string.',
        'email' => ':attribute is invalid email.',
        'url' => ':attribute is invalid url.',
        'bool' => ':attribute is invalid bool.',
    ];

    /**
     * @var MessageBag
     */
    protected $message;

    public function __construct() {
        $messages = Factory::i18n('validate');
        if (!is_array($messages)) {
            return;
        }
        $this->setMessages($messages);
        if (isset($messages['attributes'])) {
            $this->setLabels($messages['attributes']);
        }
    }

    public function setAttributes($arg) {
        $this->attributes = (array)$arg;
        return $this;
    }

    public function setRules($args) {
        foreach ((array)$args as $key => $arg) {
            $this->rules[] = $this->converterRules($arg, $key);
        }
        return $this;
    }

    public function setLabels(array $labels) {
        if (!empty($labels)) {
            $this->labels = $labels;
        }
        return $this;
    }

    public function setMessages(array $messages) {
        if (!empty($messages)) {
            $this->messages = $messages;
        }
        return $this;
    }

    /**
     * 格式化规则
     * @param $rule
     * @param null $keys
     * @return array
     */
    protected function converterRules($rule, $keys = null) {
        if (is_integer($keys) && is_array($rule)) {
            $keys = array_shift($rule);
        }
        $rules = $this->converterRule($rule);
        $rules['keys'] = $keys;
        return $rules;
    }

    /**
     * 转化一条规则
     * @param $rule
     * @return array
     */
    public function converterRule($rule) {
        if (!is_array($rule)) {
            $rule = empty($rule) ? [] : explode('|', $rule);
        }
        $rules = [];
        $message = null;
        foreach ($rule as $key => $item) {
            if (!is_integer($key)) {
                $rules[$key] = $item;
                continue;
            }
            if (is_callable($item)) {
                $rules[] = $item;
            }
            if (!is_string($item) || strpos($item, ':') === false) {
                $rules[$item] = [];
                continue;
            }
            list($key, $val) = explode(':', $item, 2);
            if ($key == 'message') {
                $message = $val;
                continue;
            }
            $rules[$key] = explode(',', $val);
        }
        return compact('rules', 'message');
    }

    /**
     * Determine if the data passes the validation rules.
     *
     * @return bool
     * @throws Exception
     */
    public function passes() {
        $this->message = new MessageBag;
        foreach ($this->rules as $item) {
            foreach ((array)$item['keys'] as $key) {
                $this->validateRule($key, isset($this->attributes[$key]) ? $this->attributes[$key] : null, $item['rules'], $item['message']);
            }
        }
        return $this->message->isEmpty();
    }

    /**
     * @param $key
     * @param $value
     * @param $rules
     * @param null $message
     * @return bool
     * @throws Exception
     */
    public function validateRule($key, $value, array $rules, $message = null) {
        if (!$this->message) {
            $this->message = new MessageBag;
        }
        foreach ($rules as $rule => $args) {
            if (is_callable($args)) {
                continue;
            }
            if (static::buildRule($rule, (array)$args)
                ->validate($value)) {
                continue;
            }
            $this->message->add($key, $this->getMessage($key, $rule, $message));
        }
        return $this->message->isEmpty();
    }

    /**
     * 获取验证错误的消息
     * @param $key
     * @param $rule
     * @param null $message
     * @return mixed
     */
    public function getMessage($key, $rule, $message = null) {
        $label = isset($this->labels[$key]) ? $this->labels[$key] : $key;
        if (!is_null($message)) {
            return str_replace(':attribute', $label, $message);
        }
        if (isset($this->messages[$key.'.'.$rule])) {
            return $this->messages[$key.'.'.$rule];
        }
        return str_replace(':attribute', $label, $this->messages[$rule]);
    }

    /**
     * Determine if the data fails the validation rules.
     *
     * @return bool
     * @throws Exception
     */
    public function fails() {
        return ! $this->passes();
    }

    /**
     * Run the validator's rules against its data.
     *
     * @param array $data
     * @return boolean
     * @throws Exception
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
     * @throws Exception
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


    public static function make(array $rules, array $data = null, array $messages = [], array $labels = []) {
        $validator = new static();
        $validator->setRules($rules)->setLabels($labels)->setMessages($messages);
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