<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use app\admin\Model\Carpos;

class CarposController extends Controller
{
    public function index(Request $request)
    {
        $data['number']=$request->param('number');
        $data['type']=$request->param('type');
        $data['phone']=$request->param('phone');
        $data['province']=$request->param('province');
        $data['province']==''?$data['province']='浙江省':$data['province'];
        $data['city']=$request->param('city');
        $data['city']==''?$data['city']='杭州市':$data['city'];
        $data['area']=$request->param('area');
        $data['area']==''?$data['area']='滨江区':$data['area'];

        $data['manager']=$request->param('manager');
        $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->order('mg_id asc')->value('mg_id');
        $data['manager']==''?$data['manager']=$manager:$data['manager'];
        $data['mg_name']=Session::get('mg_name');
        $data['mg_id']=Session::get('mg_id');
        $carpos_model=new \app\admin\model\Carpos();
        $shuju=$carpos_model->getlist($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
    }

    public function sindex(Request $request){
        $data['username']=$request->param('username');
        $data['phone']=$request->param('phone');
        $data['province']=$request->param('province');
        $data['province']==''?$data['province']='浙江省':$data['province'];
        $data['city']=$request->param('city');
        $data['city']==''?$data['city']='杭州市':$data['city'];
        $data['area']=$request->param('area');
        $data['area']==''?$data['area']='滨江区':$data['area'];

        $data['manager']=$request->param('manager');
        $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->order('mg_id asc')->value('mg_id');
        $data['manager']==''?$data['manager']=$manager:$data['manager'];
        $data['mg_name']=Session::get('mg_name');
        $data['mg_id']=Session::get('mg_id');
        $carpos_model=new \app\admin\model\Carpos();
        $shuju=$carpos_model->getslist($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
    }

     public function verify(Request $request){
        if($request->isPost()){
            $data=$request->post();
           /* if($data['style']==1){
                //此时表示审核通过，然后该用户成为个人车位用户
            }*/
            $res=\app\admin\model\Carpos::update($data);
            if($res){
                return ['status'=>'success'];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            $id=$request->param('id');
            $logo=DB::table('pk_carpos')->where(['id'=>$id])->field('logo,uid')->find();
            $this->assign(['id'=>$id,'logo'=>$logo['logo']]);
            return $this->fetch();
        }
    }

   /* //修改操作还没做完
    public function sedit(Request $request){
        if($request->isPost()){

        }else{
            $id=$request->param('id');
            $data=Carpos::alias('c')
            ->join("__USER__ u",'c.uid=u.uid','left')
            ->where(['c.id'=>$id])
            ->field('u.username,u.plate,u.phone,c.number,c.id,c.logo')->find();
            $this->assign('info',$data);
            return $this->fetch();
        }
    }*/

    //删除
    public function shanchu(){
        $id=request()->param('id');
        $res=\app\admin\model\Carpos::where(['id'=>$id])->delete();
        if(!empty($res)){
            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }
    }

    //添加个人车位
    public function personadd(){
        if(request()->isPost()){
            $mg_id=Session::get('mg_id');
            $mg_name=Session::get('mg_name');
            $data['owner']=2;
            $data['style']=1;//后台添加直接不用审核
            $data['types']=1;//个人车位
            $data['logo']=input('logo');
            if($mg_name=='admin'){
                $data['number']=input('number');
                $data['uid']=input('uid');
                $data['mg_id']=input('mg_id');
            }else{
                $data['number']=input('number');
                $data['uid']=input('uid');
                $data['mg_id']=$mg_id;
            }
            $data['type']=input('type');
            $data['uid']=str_replace('编号','',explode('-',$data['uid'])[0]);
            $province=request()->param('province');
            $city=request()->param('city');
            $area=request()->param('area');
            $carpos=new \app\admin\model\Carpos();
            $res=$carpos->save($data);
            if(!empty($res)){
                return ['status'=>'success','province'=>$province,'city'=>$city,'area'=>$area];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            $info=DB::table('pk_user')->field('uid,username')->select();
            $data['mg_name']=Session::get('mg_name');
            $data['mg_id']=Session::get('mg_id');
            $this->assign(['data'=>$data,'info'=>$info]);
            return $this->fetch();
        }
    }

    //添加物业车位
    public function wuyeadd(){
        if(request()->isPost()){
            $mg_id=Session::get('mg_id');
            $mg_name=Session::get('mg_name');
            if($mg_name=='admin'){
                $data['number']=input('number');
                $data['uid']=input('uid');
                $data['mg_id']=input('mg_id');
            }else{
                $data['number']=input('number');
                $data['uid']=input('uid');
                $data['mg_id']=$mg_id;
            }
            $data['type']=input('type');
            $data['duration']=input('duration');
            $data['charge']=input('charge');
            $data['logo']=input('logo');
            $data['owner']=1;
            $data['style']=1;//后台添加直接不用审核
            $data['types']=2;//出租车位
            $carpos=new \app\admin\model\Carpos();
            $res=$carpos->save($data);
            if(!empty($res)){
                return ['status'=>'success'];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            $data['mg_name']=Session::get('mg_name');
            $data['mg_id']=Session::get('mg_id');
            $this->assign('data',$data);
            return $this->fetch();
        }
    }

    //修改车位
    public function edit(){
        if(request()->isPost()){
            $mg_id=Session::get('mg_id');
            $mg_name=Session::get('mg_name');
            $id=request()->param('id');
            if($mg_name=='admin'){
                $data['number']=input('number');
                $data['mg_id']=input('mg_id');
                $data['uid']=input('uid');
                $data['logo']=input('logo');
            }else{
                $data['number']=input('number');
                $data['mg_id']=$mg_id;
                $data['uid']=input('uid');
                $data['logo']=input('logo');
            }
            
            if(!empty(input('duration'))){
                $data['duration']=input('duration');
            }else if(!empty(input('duration1'))){
                $data['duration1']=input('duration1');
            }
            if(!empty(input('charge'))){
                $data['charge']=input('charge');
            }else if(!empty(input('charge1'))){
                $data['charge1']=input('charge1');
            }
            $data['type']=input('type');
            $data['uid']=str_replace('编号','',explode('-',$data['uid'])[0]);
            $res=\app\admin\model\Carpos::where(['id'=>$id])->update($data);
            if(!empty($res)){
                return json(['status'=>'success']);
            }else{
                return json(['status'=>'failure']);
            }
        }else{
            $id=request()->param('id');
            $data=\app\admin\model\Carpos::alias('c')
            ->join('__USER__ u','u.uid=c.uid','left')
            ->join('__MANAGER__ m','m.mg_id=c.mg_id','left')
            ->where(['c.id'=>$id])
            ->field('c.*,u.username,m.name,m.province,m.city,m.area,c.mg_id,c.charge,c.charge1,c.types')->find();
            $shuju['mg_name']=Session::get('mg_name');
            $shuju['mg_id']=Session::get('mg_id');
            $info=DB::table('pk_user')->field('uid,username')->select();
            $this->assign(['data'=>$data,'shuju'=>$shuju,'info'=>$info,'id'=>$id]);
            return $this->fetch();
        }
    }
}
