<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Db;

class CarchargeController extends Controller
{
    
    public function index(Request $request)
    {
        $data['plate']=$request->param('plate');
        $data['reason']=$request->param('reason');
        $data['reason']==''?$data['reason']=0:$data['reason']=$data['reason'];
        $data['province']=$request->param('province');
        //$data['province']==''?$data['province']='浙江省':$data['province'];
        $data['city']=$request->param('city');
        //$data['city']==''?$data['city']='杭州市':$data['city'];
        $data['area']=$request->param('area');
        //$data['area']==''?$data['area']='滨江区':$data['area'];
        $data['manager']=$request->param('manager');
        if(!empty($data['province']) && !empty($data['city']) && !empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->column('mg_id');
        }else if(!empty($data['province']) && !empty($data['city']) && empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(!empty($data['province']) && empty($data['city']) && empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province']])->column('mg_id');//默认是显示该地区下的第一个停车场
        }else if(empty($data['province']) && empty($data['city']) && empty($data['area'])){
            $manager='';
        }
        $data['manager']==''?$data['manager']=$manager:$data['manager'];
        $data['mg_name']=Session::get('mg_name');
        $data['mg_id']=Session::get('mg_id');
        $money_model=new \app\admin\model\Carcharge();
        $shuju=$money_model->getlist($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
    }
}
