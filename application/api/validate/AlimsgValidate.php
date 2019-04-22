<?php

namespace app\api\validate;

use think\Validate;

class AlimsgValidate extends validate
{
    protected $rule = [
        'phone' => 'number',
        'code' => 'require|length:6|number',
    ];
    protected $message = [
        'phone' => '错误的手机号',
        'code' => '验证码格式错误',
    ];
}