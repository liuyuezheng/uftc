<?php

namespace app\api\controller;
use think\Controller;
use think\Session;
class BaseController extends Controller{
    //是否开启验证登录
    protected $is_login = false;
    //用户信息
    protected $api_member = [];
    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        //验证登录
        /* $this->is_login && $this->check_login();*/
    }
    /**
     * 验证登录
     * @author Steed
     */
    protected function check_login() {
        //获取session信息
        $api_member = Session::get('api_member');
        if (empty($api_member)) {
            //如果不存在session则验证cookie
             $this->redirect('api/login/index');
            //验证成功重新获取session
            $api_member = Session::get('api_member');
        }
        //检测数据完整性
        is_array($api_member) || $this->redirect('api/login/index');
        isset($api_member['id']) || $this->redirect('api/login/index');
    }
}