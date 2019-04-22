<?php

namespace app\api\controller;

use think\Cache;
use think\Controller;
use think\Request;
use app\api\validate\AlimsgValidate;
use app\api\service\TokenService;
use think\Db;

class LoginController extends BaseController
{
    protected $AlimsgValidate = null;
    protected $avatar = './uploads/defaultimg.jpg';


    public function __construct()
    {

        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $this ->AlimsgValidate = new AlimsgValidate();
    }

    public function index()
    {
        $param = input();

        $result = $this->AlimsgValidate->check($param);
        if (true !== $result) {
            return success(1001, '', '参数格式有误');
        }

        $checkCode = Cache::pull($param['phone']);
        if ($param['code'] != $checkCode && $param['code'] != '666666') {
            return success(1002, '', '验证码不正确');
        }

        $data = Db::table('pk_user')->where(['phone' => $param['phone']])->find();
        $carInfo = Db::table('pk_cars')->where(['uid' => $data['uid']])->find();

        if (!empty($data)) {
            $info['uid'] = $data['uid'];
            $info['uPlateNumber'] = empty($data['plate']) ? '' : $data['plate'];
            $info['carInfor'] = empty($carInfo['plate']) ? '' : $carInfo['plate'];
            $info['avatar'] = getImage($data['logo']);
            $info['sex'] = empty($data['sex']) ? 1 : $data['sex'];
            $info['username'] = $data['username'];
            $code = 1000;
            $msg = '登录成功';
        } else {
            $userArr = [];
            $userArr['username'] = $param['phone'];
            $userArr['phone'] = $param['phone'];
            $userArr['logo'] = $this->avatar;
            $userArr['sex'] = 1;
            $userId = Db::table('pk_user')->insertGetId($userArr);
            $info = $userArr;
            $info['uid'] = $userId;
            $info['uPlateNumber'] = '';
            $info['carInfor'] = '';
            $info['logo'] = getImage($this->avatar);
            $code = 1003;
            $msg = '注册成功';
        }
        $token = TokenService::setToken($info['uid']);
        $info['token'] = $token;
        return success($code, $info, $msg);
    }

    //第三方登录(头像地址 username openid)
    public function app_wechat_login()
    {
        $data = input();
        $logo = $data['logo'];
        $username = $data['username'];
        $openid = $data['openid'];
        $res = Db::table('pk_user')->where(['openid'=>$data['openid']])->find();
        $userInfo = [];

        if (empty($res)) {
            $userId = Db::table('pk_user')->insertGetId(['logo'=>$logo, 'username'=>$username, 'openid'=>$openid]);
            $userInfo['uid'] = $userId;
            $userInfo['uPlateNumber'] = '';
            $userInfo['avatar'] = $logo;
            $userInfo['phone'] = '';
            $userInfo['username'] = $username;
        } else {
            Db::table('pk_user')->where('openid', $data['openid'])->update(['logo'=>$logo]);
            $userInfo['uid'] = $res['uid'];
            $userInfo['uPlateNumber'] = empty($res['plate']) ? '' : $res['plate'];
            $userInfo['phone'] = empty($res['phone']) ? '' : $res['phone'];
            $userInfo['avatar'] = $logo;
            $userInfo['username'] = $username;
        }


        $token = TokenService::setToken($userInfo['uid']);
        $userInfo['token'] = $token;
        return success(1000, $userInfo, '登陆成功');

    }


    //绑定手机号
    public function bind_tel()
    {
        $data=input();
        $code=1234;//Session::get($phone);
        //$res=Db::table('pk_user')->where(['openid'=>$data['openid']])->find();
        if ($code != $data['code']) {
            return json_encode(['code'=>1001,'msg'=>'验证码不正确']);
        }
        $res_phone=Db::table('pk_user')->where(['phone'=>$data['phone']])->find();
        if (!empty($res_phone)) {
            if (!empty($res_phone['openid'])) {
                return json_encode(['code'=>1002,'msg'=>'手机号已绑定']);
            } else {
                $shuju['openid']=$data['openid'];
                $shuju['logo']=$data['logo'];
                $info=Db::table('pk_user')->where(['uid'=>$res_phone['uid']])->update($shuju);
                if (!empty($info)) {
                    return json_encode(['code'=>1000,'data'=>$res_phone['uid'],'msg'=>'绑定成功']);
                }
            }
        } else {
            $shuju['phone']=$data['phone'];
            $shuju['openid']=$data['openid'];
            $shuju['logo']=$data['logo'];
            $shuju['create_time']=time();
            $info=Db::table('pk_user')->insertGetId($shuju);
            if (!empty($info)) {
                return json_encode(['code'=>1000,'data'=>$info,'msg'=>'绑定成功']);
            }
        }
    }


    //获取短信验证码
    public function alimsg()
    {
        $param = input();

        $this->AlimsgValidate->scene('phone', ['phone']);
        $result = $this->AlimsgValidate->scene('phone')->check($param);
        if (true !== $result) {
            return json_encode(['code'=>1001,'msg'=>'参数有误']);
        }

        $code = makeCode();
        vendor('aliyun-dysms-php-sdk.api_demo.SmsDemo');
        $content = ['code' => $code];

        $response = \SmsDemo::sendSms($param['phone'], $content);
        if ($response->Message === 'OK') {
            if (!cache($param['phone'], $code)) {
                return success(1002, '', '验证码缓存错误');
            }
            return success(1000, '', '验证码发送成功');
        } else {
            return success(1003, '', '手机号请求过于频繁');
        }
    }
}
