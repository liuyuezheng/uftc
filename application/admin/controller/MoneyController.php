<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Db;

class MoneyController extends Controller
{
    public function index(Request $request)
    {
        $request->param('start_time')==''?$data['start_time']='':$data['start_time']=strtotime($request->param('start_time'));
        $request->param('end_time')==''?$data['end_time']='':$data['end_time']=strtotime($request->param('end_time'));
        $data['type']=$request->param('type');
//        $data['type']==''?$data['type']=1:$data['type']=$data['type'];
        $data['province']=$request->param('province');
//        //$data['province']==''?$data['province']='浙江省':$data['province'];
        $data['city']=$request->param('city');
//        //$data['city']==''?$data['city']='杭州市':$data['city'];
        $data['area']=$request->param('area');
        //$data['area']==''?$data['area']='滨江区':$data['area'];
       $data['manager']=$request->param('manager');
        /*$data['mg_ids']=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->column('mg_id');*/
//        if(!empty($data['province']) && !empty($data['city']) && !empty($data['area'])){
//            $data['mg_ids']=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->column('mg_id');
//        }else if(!empty($data['province']) && !empty($data['city']) && empty($data['area'])){
//            $data['mg_ids']=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city']])->column('mg_id');
//        }else if(!empty($data['province']) && empty($data['city']) && empty($data['area'])){
//            $data['mg_ids']=Db::table('pk_manager')->where(['province'=>$data['province']])->column('mg_id');//默认是显示该地区下的第一个停车场
//        }else if(empty($data['province']) && empty($data['city']) && empty($data['area'])){
//            $data['mg_ids']='';
//        }
        $data['mg_name']=Session::get('mg_name');
        $data['mg_id']=Session::get('mg_id');
        $money_model=new \app\admin\model\Money();
        $shuju=$money_model->getlist($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
//                $data['mg_name']=$request->param('mg_name');
//        $data['mg_id']=$request->param('mg_id');
//                $money_model=new \app\admin\model\Money();
//        $shuju=$money_model->getlist($data);
//        return json_encode($shuju);
    }
}
