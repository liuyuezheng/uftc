<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\model\admin\Recharge;

class RechargeController extends Controller
{
    public function index(Request $request)
    {
        $data['username']=$request->param('username');
        $request->param('start_time')==''?$data['start_time']='':$data['start_time']=strtotime($request->param('start_time'));
        $request->param('end_time')==''?$data['end_time']='':$data['end_time']=strtotime($request->param('end_time'));
        $money_model=new \app\admin\model\Recharge();
        $shuju=$money_model->getlist($data);
        $this->assign('shuju',$shuju);
        return $this->fetch();
    }

    
}
