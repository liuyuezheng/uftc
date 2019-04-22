<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
use app\api\Model\Tixian;
use app\api\Model\Manager;
use app\api\Model\User;
use app\api\Controller\AlitixianController;
use think\Db;

class TixianController extends Controller
{
    public function index(Request $request)
    {
        $data['type']=$request->param('type');
        $data['type']==''?$data['type']=1:$data['type']=$data['type'];
        $request->param('start_time')==''?$data['start_time']='':$data['start_time']=strtotime($request->param('start_time'));
        $request->param('end_time')==''?$data['end_time']='':$data['end_time']=strtotime($request->param('end_time'));
        $data['status']=$request->param('status');
        $data['status']==''?$data['status']=0:$data['status']=$data['status'];
        $data['province']=$request->param('province');
        //$data['province']==''?$data['province']='浙江省':$data['province'];
        $data['city']=$request->param('city');
        //$data['city']==''?$data['city']='杭州市':$data['city'];
        $data['area']=$request->param('area');
        //$data['area']==''?$data['area']='滨江区':$data['area'];
        $data['manager']=$request->param('manager');
        $data['mg_name']=Session::get('mg_name');
        $data['mg_id']=Session::get('mg_id');
        $tixian_model=new \app\admin\model\Tixian();
        $shuju=$tixian_model->getlist($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
    }

    public function verify(Request $request){
        if($request->isPost()){
            $data=$request->post();
            $res=Tixian::update($data);
            if($res){
                return ['status'=>'success'];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            $id=$request->param('id');
            $this->assign(['id'=>$id]);
            return $this->fetch();
        }
    }

   /* //物业提现
    public function windex(){
        $data['mg_id']=session::get('mg_id');
        $tixian_model=new \app\admin\model\Tixian();
        $shuju=$tixian_model->getwlist($data);
        $this->assign('shuju',$shuju);
        return $this->fetch();
    }*/

    //提现申请
    public function add(){
        if(request()->isPost()){
            $data=request()->post();

            $data['type']=1;
            $data['mg_id']=Session::get('mg_id');
            $data['status']=3;
            $data['duration']=time();
            $data['order_id']=date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

            $exists=\app\admin\model\Manager::where(['mg_id'=>$data['mg_id']])->field('alipay,moneybag,truename')->find()->toarray();


            if($exists['moneybag']<$data['account']){
                return ['status'=>'failure','errorinfo'=>'余额不足'];
            }

            if(!empty($exists['alipay'])){
                $data['alipay']=$exists['alipay'];
                $data['truename']=$exists['truename'];
            }else{
                $info['alipay']=$data['alipay'];
                $info['truename']=$data['truename'];
                $res1=Db::table('pk_manager')->where(['mg_id'=>$data['mg_id']])->update($info);
            }

            $res=\app\admin\model\Tixian::insert($data);
            if(!empty($res)){
                return ['status'=>'success'];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            $mg_id=request()->param('mg_id');
            $data=\app\admin\model\Manager::where(['mg_id'=>$mg_id])->field('alipay,truename')->find()->toarray();
            $this->assign('data',$data);
            return $this->fetch();
        }
    }

    public function ispay(){
        $id=request()->param('goods_id');
        $shuju['ispay']=1;
        $shuju['status']=1;
        $data=\app\admin\model\Tixian::where(['id'=>$id])->field('type,account,mg_id,uid,alipay,truename')->find()->toArray();
        if($data['type']==1){
            //此时表示是停车场提现
            $res=Db::table('pk_manager')->where(['mg_id'=>$data['mg_id']])->setDec('moneybag',$data['account']);
        }else if($data['type']==2){
            //此时表示用户提现
            $res=Db::table('pk_user')->where(['uid'=>$data['uid']])->setDec('moneybag',$data['account']);
        }
        $r=Db::table('pk_tixian')->where(['id'=>$id])->update($shuju);
        if(!empty($res)){
            if($data['type']==2){
                $adds=array(
                    'uid'=>$data['uid'],
                    'title'=>'系统消息',
                    'message'=>"提现成功",
                    'createtime'=>time(),
                    'updatetime'=>time(),
                    'type'=>6
                );
                Db::table('pk_message')->insert($adds);

            }else{
                $adds=[];
            }
            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }
    }

    public function ispay1(){
        $id=request()->param('goods_id');
        $data=\app\admin\model\Tixian::where(['id'=>$id])->field('order_id,type,account,mg_id,uid,alipay,truename')->find()->toArray();
        $res=$this->userWithDraw($data['order_id'],$data['alipay'],$data['account'],$data['truename'],$data['type']);
        if($res){
            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }
    }
}
