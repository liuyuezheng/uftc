<?php

namespace app\api\validate;


use think\Validate;

class IndexValidate extends validate
{
    protected $rule = [
        'uid' => 'require|number',
        'carImg' => 'require',
        'brand' => 'require',
        'version' => 'require',
        'displacement' => 'require',
        'plate' => ['regex'=>'/[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领A-Z]{1}[A-Z]{1}[A-Z0-9]{4}[A-Z0-9挂学警港澳]{1}/'],
    ];

    protected $message = [
        'uid' => 'uid错误',
        'carImg' => 'carImg错误',
        'brand' => 'brand错误',
        'version' => 'version错误',
        'displacement' => 'displacement错误',
        'plate' => 'plate错误',
    ];
}
