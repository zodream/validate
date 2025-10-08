<?php
declare(strict_types=1);
namespace Zodream\Validate;

/**
 * Created by PhpStorm.
 * User: zx648
 * Date: 2016/12/6
 * Time: 12:16
 */

use ArrayAccess;
use Exception;
use Zodream\Infrastructure\Support\MessageBag;
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

    protected array $attributes = [];
    /**
     * @var array{keys:string[],rules:string[][],message: string}[]
     */
    protected array $rules = [];
    protected array $labels = [];
    protected array $messages = [
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
     * @var MessageBag|null
     */
    protected MessageBag|null $message = null;

    public function __construct() {
        if (!function_exists('trans')) {
            return;
        }
        $messages = trans('validate');
        if (!is_array($messages)) {
            return;
        }
        $this->setMessages($messages);
        if (isset($messages['attributes'])) {
            $this->setLabels($messages['attributes']);
        }
    }

    public function setAttributes(array $arg) {
        $this->attributes = $arg;
        return $this;
    }

    public function setRules(array|string $args) {
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
     * @param string|array|null $rule
     * @param int|array|null $keys
     * @return array
     */
    protected function converterRules(null|string|array $rule, int|array|null $keys = null): array {
        if (is_integer($keys) && is_array($rule)) {
            $keys = array_shift($rule);
        }
        $rules = $this->converterRule($rule);
        $rules['keys'] = $keys;
        return $rules;
    }

    /**
     * 转化一条规则
     * @param array|string|null $rule
     * @return array
     */
    public function converterRule(array|null|string $rule): array {
        if (!is_array($rule)) {
            $rule = empty($rule) ? [] : explode('|', $rule);
        }
        $rules = [];
        $message = null;
        foreach ($rule as $key => $item) {
            if ($key === 'message') {
                $message = $item;
                continue;
            }
            if (!is_integer($key)) {
                $rules[$key] = $item;
                continue;
            }
            if (is_callable($item)) {
                $rules[] = $item;
            }
            if (!is_string($item) || !str_contains($item, ':')) {
                $rules[$item] = [];
                continue;
            }
            list($key, $val) = explode(':', $item, 2);
            if ($key === 'message') {
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
    public function passes(): bool {
        $this->message = new MessageBag();
        foreach ($this->rules as $item) {
            foreach ((array)$item['keys'] as $key) {
                $this->validateRule($key, $this->attributes[$key] ?? null, $item['rules'], $item['message']);
            }
        }
        return $this->message->isEmpty();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param array $rules
     * @param null $message
     * @return bool
     * @throws Exception
     */
    public function validateRule(string $key, mixed $value, array $rules, $message = null): bool {
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
     * @param string $key
     * @param string $rule
     * @param string|null $message
     * @return string
     */
    public function getMessage(string $key, string $rule, string|null $message = null): string {
        $label = $this->labels[$key] ?? $key;
        if (!is_null($message)) {
            return str_replace(':attribute', $label, $message);
        }
        if (isset($this->messages[$key.'.'.$rule])) {
            return $this->messages[$key.'.'.$rule];
        }
        if (isset($this->messages[$rule])) {
            return str_replace(':attribute', $label, $this->messages[$rule]);
        }
        return sprintf('%s %s error!', $label, $rule);
    }

    /**
     * Determine if the data fails the validation rules.
     *
     * @return bool
     * @throws Exception
     */
    public function fails(): bool
    {
        return ! $this->passes();
    }

    /**
     * Run the validator's rules against its data.
     *
     * @param array $data
     * @return boolean
     * @throws Exception
     */
    public function validate(array $data = []): bool
    {
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
    public function messages(): MessageBag {
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


    public static function make(array $rules, array|null $data = null, array $messages = [], array $labels = []): bool|Validator {
        $validator = new static();
        $validator->setRules($rules)->setLabels($labels)->setMessages($messages);
        if (is_null($data)) {
            return $validator;
        }
        return $validator->validate($data);
    }

    /**
     * @param array|ArrayAccess $data
     * @param array $rules
     * @return array
     * @throws ValidationException
     */
    public static function filter(mixed $data, array $rules): array {
        $items = [];
        $validator = new static();
        foreach ($rules as $key => $rule) {
            $rule = $validator->converterRule($rule);
            $value = $data[$key] ?? null;
            if ((is_null($value) || $value === '') && !isset($item['rules']['required'])) {
                continue;
            }
            if ($validator->validateRule($key, $value, $rule['rules'], $rule['message'])) {
                $items[$key] = $value;
            }
        }
        if ($validator->messages()->isEmpty()) {
            return $items;
        }
        throw new ValidationException($validator);
    }

    /**
     * @param string $ruleName
     * @param array $arguments
     * @return RuleInterface
     * @throws Exception
     */
    public static function buildRule(string $ruleName, array $arguments = []) {
        return RuleFactory::getInstance()->rule($ruleName, $arguments);
    }

    public static function __callStatic(string $ruleName, array $arguments) {
        return static::buildRule($ruleName, $arguments);
    }
}