<?php
/**
 * Created by PhpStorm.
 * User: #ZhangQiang
 * Date: 2018/9/1
 * Time: 16:12
 */

namespace app\api\library;


use think\Exception;

class ApiException extends Exception
{
    public $code = 404;
    public $msg = '请求未找到';
    public $errorCode = 10000;

    public function __construct($params = [])
    {
        if (!is_array($params)) {
            return ;
        }
        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }
        if (array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];
        }
        if (array_key_exists('errorCode', $params)) {
            $this->errorCode = $params['errorCode'];
        }
    }
}