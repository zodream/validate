# validate
表单数据验证器

## 使用方法

自定义使用

```PHP

Validator::make([
    'name' => 'required',
    'name' => ['required', 'message' => '用户名必填']
    [['name', 'url'], 'required', 'max' => 5],
    'name' => 'required|string|min:5|message:用户名长度必须大于5个字符'
])->validate($data);  // bool

Validator::make([
    'name' => 'required',
], $data); // 简写 bool

Validator::required()->valdate($value); // bool

$v = Validator::make([]);
$v->validate(); // bool
$v->errors();    // ['name' => ['错误信息']]
$v->firstError(); // '错误信息'
```

配合 Model 使用

```PHP

class Do extends Model {
    public function rules() {
        return [
            'name' => 'required',   // 一行只能验证一个属性
            'url' => 'required|url|max:255'
        ];
    }

    public function messages() {
        return [
            'required' => ':attribute 必填',
            'name.required' => '用户名必填！',
        ];
    }

    public function labels() {
        return [
            'name' => '用户名'
        ];
    }

}

```

获取验证过的 提交字段

```PHP

Request::validate([
    'name' => 'required'
]); // ['name' => '值']

```

## 验证方法

required 必填

```PHP

Validator::required()->validate('1'); // true

```
