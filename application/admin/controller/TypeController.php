<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class TypeController extends Controller
{
    
    public function index()
    {
        $data = Db::table('pk_type')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function add(){
        if(Request()->isPost()){
            $data=input();
            $res=Db::table('pk_type')->insert($data);
            if(!empty($res)){
                return ['status'=>'success'];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            return $this->fetch();
        }
    }

    public function edit(){
        if(request()->isPost()){
            $data=input();
            $res= Db::table('pk_type')->update($data);
            if(!empty($res)){
                return ['status'=>'success'];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            $id=request()->param('id');
            $data = Db::table('pk_type')->where(['id'=>$id])->find();
            $this->assign('data',$data);
            return $this->fetch();
        }
    }

    public function shanchu(){
        $id=request()->param('id');
        $res=Db::table('pk_type')->where(['id'=>$id])->delete();
        if(!empty($res)){
            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }
    }
}
