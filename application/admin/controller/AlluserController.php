<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;

class AlluserController extends Controller
{
    public function index(Request $request)
    {
        $data['username']=$request->param('username');
        $data['phone']=$request->param('phone');

        $data1['mg_name']=Session::get('mg_name');
        $data1['mg_id']=Session::get('mg_id');
        $user_model=new \app\admin\model\Alluser();
        $shuju=$user_model->getlist($data,$data1);
//        dump($shuju);
        $this->assign('shuju',$shuju);
        $this->assign('list',$shuju['page']['data']);
        return $this -> fetch();
//        $data1['mg_name']=$request->param('mg_name');
//        $data1['mg_id']=$request->param('mg_id');
//        $user_model=new \app\admin\model\Alluser();
//        $shuju=$user_model->getlist($data,$data1);
//        return json($shuju);
   }
}
