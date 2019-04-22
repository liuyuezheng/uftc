<?php

namespace app\api\controller;
use think\Controller;
use think\Db;

// 相机对接
class HardController extends Controller
{

    public function __construct()
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
    }

    public function test()
    {
        echo '{"status":200,"speed":1,"postfix":"\u4eacABC123\u6b22\u8fce\u5149\u4e34","verified":true}';
    }

    // 进如停车场场接口
     public function enter()
     {
         $data = input("post.");
//         dump($data);
         return json_encode(['status'=>200,'speed'=>1,'postfix' =>$data['car_plate'].'欢迎光临','verified'=>true]);
     }
}