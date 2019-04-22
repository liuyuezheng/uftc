<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;

class SensorController extends Controller
{
    public function index(Request $request)
    {
        $data['province']=$request->param('province');
        //$data['province']==''?$data['province']='浙江省':$data['province'];
        $data['city']=$request->param('city');
        //$data['city']==''?$data['city']='杭州市':$data['city'];
        $data['area']=$request->param('area');
        //$data['area']==''?$data['area']='滨江区':$data['area'];

        $data['manager']=$request->param('manager');
        if(!empty($data['province']) && !empty($data['city']) && !empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->column('mg_id');//默认是显示该地区下的第一个停车场
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
        $carpos_model=new \app\admin\model\Sensor();
        $shuju=$carpos_model->getList($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch(); 
    }

    public function add(Request $request){
        if($request->isPost()){
            $mg_name=Session::get('mg_name');
            $mg_id=Session::get('mg_id');
            $data=$request->post();
            if($mg_name=='admin'){
                $shuju=[
                    'serialno'=>$data['serialno'],
                    'mg_id'=>$data['mg_id'],
                    'pid'=>$data['cid'],
                ];
                $res=Db::table('pk_sensor')->insert($shuju);
            }else{
                $shuju=[
                    'serialno'=>$data['serialno'],
                    'mg_id'=>$mg_id,
                    'pid'=>$data['cid'],
                ];
                $res1=Db::table('pk_sensor')->insert($shuju);
            }
            
            if(!empty($res)){
                return json(['status'=>1,'province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area'],'manager'=>$data['mg_id']]);
            }else if(!empty($res1)){
                return json(['status'=>2]);
            }else{
                return json(['status'=>3]);
            }
        }else{
            $data['mg_name']=Session::get('mg_name');
            $data['mg_id']=Session::get('mg_id');
            //$shuju=Db::table('pk_carpos')->where(['mg_id'])
            $this->assign(['data'=>$data]);
            return $this->fetch();
        }
    }

    public function getcarpos(Request $request){
        $manager=$request->param('manager');
        $data=Db::table('pk_carpos')->where(['mg_id'=>$manager])->field('id,number')->select();
        return ['data'=>$data];
    }

    public function edit(Request $request){
        if($request->isPost()){
            $mg_name=Session::get('mg_name');
            $mg_id=Session::get('mg_id');
            $data=$request->post();
            if($mg_name=='admin'){
                $shuju=[
                    'serialno'=>$data['serialno'],
                    'mg_id'=>$data['mg_id'],
                    'pid'=>$data['cid'],
                ];
                $res=Db::table('pk_sensor')->where(['id'=>$data['id']])->update($shuju);
            }else{
                $shuju=[
                    'serialno'=>$data['serialno'],
                    'mg_id'=>$mg_id,
                    'pid'=>$data['cid'],
                ];
                $res1=Db::table('pk_sensor')->where(['id'=>$data['id']])->update($shuju);
            }

            if(!empty($res)){
                return ['status'=>1,'province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area'],'manager'=>$data['mg_id']];
            }else if(!empty($res1)){
                return ['status'=>2];
            }else{
                return ['status'=>3];
            }
        }else{
            $id=$request->param('id');
            $data=Db::table('pk_sensor')->where(['id'=>$id])->find();
            $data['mg_name']=Session::get('mg_name');
            //$data['mg_ids']=Session::get('mg_id');
            $data['id']=$id;
            $this->assign('data',$data);
            return $this->fetch();
        }
    }

    public function delete(Request $request){
        $id=$request->param('id');
        $res=Db::table('pk_sensor')->where(['id'=>$id])->delete();
        if(!empty($res)){
            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }
    }

    public function getsc(Request $request){
        $province=$request->param('province');
        $city=$request->param('city');
        $data=Db::table('pk_manager')->where(['province'=>$province,'city'=>$city])->field('mg_id,name')->select();
        return ['data'=>$data];
    }

    public function gets(Request $request){
        $province=$request->param('province');
        $data=Db::table('pk_manager')->where(['province'=>$province])->field('mg_id,name')->select();
        return ['data'=>$data];
    }
}
