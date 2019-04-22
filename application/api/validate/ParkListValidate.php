<?php

namespace app\api\validate;

use think\Validate;

class ParkListValidate extends validate
{
    protected $rule = [
        'uid' => 'require',
        'orderId' => 'require',
        'types' => 'number',
        'mg_id' => 'require',
        'id' => 'require',
        'pid' => 'require|number|gt:0',
        'uid' => 'require|number',
        'alinumber' => 'require|number',
        'truename' => 'require',
        'flag' => 'in:1,2,3',
        'longitude' => 'require|float',
        'latitude' => 'require|float',
    ];
    protected $message = [
        'uid' => 'uid错误sadsad',
        'types' => 'types错误',
        'mg_id' => 'mg_id错误',
        'alinumber' => '支付宝账号错误',
        'truename' => '支付宝真实姓名',
        'id' => 'id错误1',
        'pid' => 'pid错误',
        'orderId' => 'orderId错误',
        'flag' => 'flag错误',
        'longitude' => 'longitude错误',
        'latitude' => 'latitude错误',
    ];
}