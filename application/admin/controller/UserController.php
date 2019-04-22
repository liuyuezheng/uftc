<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\admin\model\User;
use think\Db;
use think\Session;

class UserController extends Controller
{
    //获取租赁用户个人信息
    public function rentindex(Request $request)
    {
        $data['username']=$request->param('username');
        $data['phone']=$request->param('phone');
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
        $user_model=new \app\admin\model\User();
        $shuju=$user_model->getlist($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
//        $data['mg_name']=$request->param('mg_name');
//        $data['mg_id']=$request->param('mg_id');
//        $user_model=new \app\admin\model\User();
//        $shuju=$user_model->getlist($data);
//        return json_encode($shuju);

    }

     public function rentadd(Request $request){
      if($request->ispost()){
            $data=input();
            $data['type']=2;
            $user_model=new \app\admin\model\User();
            $flag=$user_model->add($data);           
            if($flag==1){
                return ['status'=>1];
            }else if($flag==2){
                return ['status'=>2];
            }
        }
        return $this->fetch('User/rentadd');
    }

     //获取修改信息
    public function rentedit(){
        $data = input('uid');
        $user_model = new \app\admin\model\User();
        $res = $user_model->edit($data);
        $res->start_time=date('Y-m-d H:i:s',$res->start_time);
        $res->end_time=date('Y-m-d H:i:s',$res->end_time);
        if(!empty($res)){
           $this->assign('res',$res);
           return $this->fetch('User/rentadd');
        }
    }


    //获取个人车位用户审核信息
    public function personsindex(Request $request)
    {
        $data['username']=$request->param('username');
        $data['phone']=$request->param('phone');
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
        $user_model=new \app\admin\model\User();
        $shuju=$user_model->getpsList($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
//        $data['mg_name']=$request->param('mg_name');
//        $data['mg_id']=$request->param('mg_id');
//        $user_model=new \app\admin\model\User();
//        $shuju=$user_model->getpsList($data);
//        return json_encode($shuju);
    }

    public function verify(Request $request){
        if($request->isPost()){
            $data=$request->post();
            $res=\app\admin\model\User::update($data);
            if($res){
                return ['status'=>'success'];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            $uid=$request->param('uid');
            $this->assign(['uid'=>$uid]);
            return $this->fetch();
        }
    }

    public function personsadd(Request $request){
      if($request->ispost()){
            $data=input();
            if(strpos($data['logo'],'goodstmp')!==false){
                //删除(有的情况)旧图片
                if(!empty($data['logo'])){
               
                $dir="./uploads/goods/".date('Ymd');
                if(!file_exists($dir)){
                    mkdir($dir,0777,true);
                }        
            }
                //移动上传图片到真实位置，并存储真实位置到数据库
                $truepath = str_replace('goodstmp','goods',$data['logo']);
                copy($data['logo'],$truepath);
                $data['logo'] = $truepath; //修改为真实的图片路径名
            }
            $data['create_time']=time();
            $data['type']=1;
            $data['flag']=3;//dump($data);die;
            $user_model=new \app\admin\model\User();
            $flag=$user_model->psadd($data);           
            if($flag==1){
                return ['status'=>1];
            }else if($flag==2){
                return ['status'=>2];
            }
        }
        return $this->fetch('User/personsadd');
    }

     //获取修改信息
    public function personsedit(){
        $data = input('uid');
        $user_model = new \app\admin\model\User();
        $res = $user_model->psedit($data);
        if(!empty($res)){
           $this->assign('res',$res);
           return $this->fetch('User/personsadd');
        }
    }




     //获取个人车位用户信息
    public function personindex(Request $request)
    {
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
        $user_model=new \app\admin\model\User();
        $shuju=$user_model->getpList($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
    }

     public function personadd(Request $request){
      if($request->ispost()){
            $data=input();
            if(strpos($data['logo'],'goodstmp')!==false){
                //删除(有的情况)旧图片
                if(!empty($data['logo'])){
               
                $dir="./uploads/goods/".date('Ymd');
                if(!file_exists($dir)){
                    mkdir($dir,0777,true);
                }        
            }
                //移动上传图片到真实位置，并存储真实位置到数据库
                $truepath = str_replace('goodstmp','goods',$data['logo']);
                copy($data['logo'],$truepath);
                $data['logo'] = $truepath; //修改为真实的图片路径名
            }
            $data['create_time']=time();
            $data['type']=1;
            $data['flag']=1;
            $user_model=new \app\admin\model\User();
            $flag=$user_model->padd($data);           
            if($flag==1){
                return ['status'=>1];
            }else if($flag==2){
                return ['status'=>2];
            }
        }
        return $this->fetch('User/personadd');
    }

     //获取修改信息
    public function personedit(){
        $data = input('uid');
        $user_model = new \app\admin\model\User();
        $res = $user_model->pedit($data);
        if(!empty($res)){
           $this->assign('res',$res);
           return $this->fetch('User/personadd');
        }
    }

    public function getscc(Request $request){
        $province=$request->param('province');
        $city=$request->param('city');
        $district=$request->param('district');
        $data=Db::table('pk_manager')->where(['province'=>$province,'city'=>$city,'area'=>$district])->field('mg_id,name')->select();
        return ['data'=>$data];
    }

    public function shanchu(Request $request)
    {
        $id = $request -> param('id');
        $result = \app\admin\model\User::where(['uid'=>$id]) -> delete();
        if($result){
            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }
    }

     /*
     * 承接 添加/修改 任务两个地方uploadify上传图片处理
     * 接收uploadify上传的图片，并处理添加到服务器上
     * 图片最终保存在 public/uploads/goods/wlek23k2okwkdlw.jpg
     */
    public function logo_up(Request $request)
    {
        //接收图片的附件信息
        //$request -> file();  接收上传的附件域信息 
        $file = $request -> file('Filedata');  //返回think\File对象

        //把附件从临时位置挪到真是位置去,$file对象中有move方法可以实现
        $path = "./uploads/goodstmp/"; //附件存储位置(临时的)
        $result = $file->move($path); //系统会给附件创建一个随机的字符串名字
                                    //move()方法上传成功返回当前$file对象，否则false
        if($result){
            //上传成功，返回当前上传的附件路径名 用于给客户端使用
            //附件上传成功后的路径吗效果：./uploads/goods/20171231/wliedll232ekw2ewo2.jpg
            $logopathname = $path.$result->getSaveName();
            $logopathname = str_replace("\\","/",$logopathname);//替换"\"为"/"
            $info = ['status'=>'success','logopathname'=>$logopathname];
            echo json_encode($info);
        }else{
            //上传失败，返回错误信息即可
            $info = ['status'=>'failure','errorinfo'=>$result->getError()];
            echo json_encode($info);
        }
        exit; //禁止后续跟踪信息获取
    }
}
