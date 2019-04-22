<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Db;

class PasswordController extends Controller
{

    public function edit(Request $request)
    {
        if($request->isPost()){
            $mg_id=Session::get('mg_id');
            $data=$request->post();
            if($data['password']==$data['password1']){
                $shuju['password']=md5($data['password']);
                $res=Db::table('pk_manager')->where(['mg_id'=>$mg_id])->update($shuju);
                return ['status'=>'success'];
            }else{
                return ['status'=>'failure','errorinfo'=>'两次密码不一致'];
            }
        }
        return $this->fetch();
    }

    public function verify(Request $request){
        $password=md5($request->param('password'));
        $mg_name=Session::get('mg_name');
        $mg_id=Session::get('mg_id');
        $data['password']=Db::table('pk_manager')->where(['mg_id'=>$mg_id])->value('password');
        if($password==$data['password']){
            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }
    }

}
