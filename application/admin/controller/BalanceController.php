<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class BalanceController extends Controller
{
    public function index(Request $request)
    {
        $data['type']=$request->param('type');
        $data['type']==''?$data['type']=1:$data['type']=$data['type'];
        $data['name']=$request->param('name');
        $balance_model=new \app\admin\model\Balance();
        $shuju=$balance_model->getlist($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
    }
}
