<?php

namespace app\api\validate;

use think\Validate;

class UserValidate extends validate
{
    protected $rule = [
        'uid' => 'require|number',
        'pid' => 'require|number',
        'account' => 'require',
        'pay' => 'require',
        'province' => 'require',
        'city' => 'require',
        'fromId' => 'require|number',
        'charge' => 'require',
        'end_time' => 'require',
        'start_time' => 'require',

    ];
    protected $message = [

    ];
}