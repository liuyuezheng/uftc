<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class AccountController extends Controller
{

    public function index(Request $request)
    {
        $data['type']=$request->param('type');
        $data['type']==''?$data['type']=1:$data['type']=$data['type'];
        $data['status']=$request->param('status');
        $data['status']==''?$data['status']=0:$data['status']=$data['status'];
        $data['province']=$request->param('province');
        //$data['province']==''?$data['province']='浙江省':$data['province'];
        $data['city']=$request->param('city');
        //$data['city']==''?$data['city']='杭州市':$data['city'];
        $data['area']=$request->param('area');
        //$data['area']==''?$data['area']='滨江区':$data['area'];
        $data['manager']=$request->param('manager');
        $tixian_model=new \app\admin\model\Account();
        $shuju=$tixian_model->getlist($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
    }

    public function edit(Request $request){
        if($request->isPost()){
            $data=$request->post();
            if($data['type']==1){
                //此时表示停车场
                $shuju['alipay']=$data['alipay'];
                $shuju['truename']=$data['truename'];
                $res=Db::table('pk_manager')->where(['mg_id'=>$data['id']])->update($shuju);
            }else if($data['type']==2){
                //此时表示用户
                $shuju['alinumber']=$data['alinumber'];
                $shuju['username']=$data['username'];
                $res=Db::table('pk_user')->where(['uid'=>$data['id']])->update($shuju);
            }
            if(!empty($res)){
                return ['status'=>'success','type'=>$data['type']];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            $id=$request->param('id');
            $type=$request->param('type');
            if($type==1){
                //此时是停车场
                $data=Db::table('pk_manager')->where(['mg_id'=>$id])->field('name,alipay,truename')->find();
            }else if($type==2){
                //此时是用户
                $data=Db::table('pk_user')->where(['uid'=>$id])->field('alinumber,username')->find();
            }
            $this->assign(['data'=>$data,'type'=>$type,'id'=>$id]);
            return $this->fetch();
        }
    }
}
