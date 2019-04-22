<?php

namespace app\admin\controller;

use app\admin\model\Permission;
use app\admin\model\Role;
use think\Controller;
use think\Request;
use think\Validate;

class PermissionController extends Controller
{
   public function index(){
       $info=Permission::select();
       //dump($info);
       $arr=[];
       foreach($info as $v){
           $arr[]=$v->toArray();
       }
       //dump($arr);
       $shuju=generateTree($arr);
       $this->assign('info',$shuju);
       return $this->fetch();
   }

    public function tianjia()
    {
        if(request()->isPost()){
            $shuju=request()->post();
            //dump($shuju);
            $rules=[
                'ps_name'=>'require',
            ];
            $notices=[
                'ps_name.require'=>'权限名称必填',
            ];
            $validate=new Validate($rules,$notices);
            if($validate->batch()->check($shuju)){
                if($shuju['ps_pid']==0){
                    $shuju['ps_level']=0;
                }else{
                    $p_level=Permission::where('ps_id',$shuju['ps_pid'])->value('ps_level');
                    $shuju['ps_level']=$p_level+1;
                }
                //dump($shuju);
                $permission=new Permission();
                $result=$permission->allowField(true)->save($shuju);
                if($result){
                    return ['status'=>'success'];
                }else{
                    return ['status'=>'failure','errorinfo'=>'数据写入失败'];
                }
            }else{
                $errorinfo=$validate->getError();
                return ['status'=>'failure','errorinfo'=>implode(',',$errorinfo)];
            }

        }else{
            $info=Permission::where('ps_level','in','0,1')
                ->select();
            //dump($info);
            $arr=[];
            foreach($info as $v){
                $arr[]=$v->toArray();
            }
            $info=generateTree($arr);
            //dump($info);
            $this->assign('info',$info);
            return $this->fetch();
        }
   }

    public function xiugai(Request $request,Permission $permission)
    {
        if($request->isPost()){
            $shuju=$request->post();
            $shuju['ps_id']=$request->param('ps_id');
            //dump($shuju);
            $result=$permission->update($shuju);
            if($result){
                return ['status'=>'success'];
            }else{
                return ['status'=>'failure','errorinfo'=>'数据写入失败'];
            }
        }else{
            $info=Permission::select();
            //dump($info);
            $arr=[];
            foreach($info as $v){
                $arr[]=$v->toArray();
            }
            $info=generateTree($arr);
            //dump($info);
            $this->assign('info',$info);
            $this->assign('psinfo',$permission);
            return $this->fetch();
        }
   }

    public function shanchu(Request $request,Permission $permission)
    {
        $ps_pid=$permission->ps_id;
        //dump($ps_pid);
        $ps_info=Permission::where('ps_pid',$ps_pid)->select();
         if($ps_info==[]){
             $role_info=implode(',',Role::column('role_ps_ids'));
             if(strstr($role_info,(string)$ps_pid)){
                return ['status'=>'failure','errorinfo'=>'该权限是角色拥有权限,不能删除'];
             }else {
                  $result = $permission->delete();
                  if ($result) {
                      return ['status' => 'success'];
                  } else {
                      return ['status' => 'failure'];
                  }
              }
         }else{
             return ['status'=>'failure','errorinfo'=>'该权限有下属权限,不能删除'];
         }
   }
}
