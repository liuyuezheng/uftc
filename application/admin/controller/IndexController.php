<?php
namespace app\admin\controller;

use app\admin\model\Manager;
use app\admin\model\Permission;
use think\Controller;
use think\Db;


class IndexController extends Controller
{
    //后台首页
    public function index()
    {
        //获得管理员
        $mg_id      = session('mg_id');
        $mg_name    = session('mg_name');

        if(empty($mg_id)){
            $this->redirect('admin/manager/login');
        }

        //判断是否是超级管理员admin，是admin就获得全部权限
        if($mg_name==='admin'){
            //获得全部权限
            $ps_infoA = Permission::where('ps_level','0')
                ->select();
            $ps_infoB =Permission::where('ps_level','1')
                ->select();
        } else{
            //获得管理员角色
            //获得管理员角色对应的操作权限的id信息
            $ps_ids = \app\admin\model\Manager::alias('m')
                ->join('__ROLE__ r','m.role_id=r.role_id')
                ->where('m.mg_id',$mg_id)
                ->value('r.role_ps_ids');

            $ps_infoA = \app\admin\model\Permission::where('ps_id','in',$ps_ids)
                ->where('ps_level','0')
                ->select();
            $ps_infoB =\app\admin\model\Permission::where('ps_id','in',$ps_ids)
                ->where('ps_level','1')
                ->select();
        }

        $this -> assign('ps_infoA',$ps_infoA);
        $this -> assign('ps_infoB',$ps_infoB);
        return $this ->fetch();
    }

    //右侧单独方法
    public function welcome() 
    {
        $mg_id      = session('mg_id');
        $mg_name    = session('mg_name');
        if($mg_name == 'admin'){
             //停车场数量
            $manager_number = Db::table('pk_manager')->count('mg_id')-1;
            $people_number = Db::table('pk_user')->count('uid');
            //用户数量
            $data=Db::table('pk_manager')->where(['mg_name'=>array('neq','admin')])->field('mg_id,name')->select();
            foreach($data as $k=>$v){
//                $data[$k]['unum']=DB::table('pk_stopcarrecord')->where(['mg_id'=>$v['mg_id']])->group('uid')->count('uid');
                $num1=DB::table('pk_carpos')->where(['mg_id'=>$v['mg_id']])->where('uid','neq',0)->group('uid')->count('uid');
                $num2=DB::table('pk_carpos')->where(['mg_id'=>$v['mg_id']])->where('uuid','neq',0)->group('uuid')->count('uuid');
                $num3=DB::table('pk_carpos')->where(['mg_id'=>$v['mg_id']])->where('uuuid','neq',0)->group('uuuid')->count('uuuid');
                $num4=DB::table('pk_stopcarrecord')->where(['mg_id'=>$v['mg_id'],'status'=>4])->group('uid')->count('uid');
                $data[$k]['unum']=$num1+$num2+$num3+$num4;
//                $data[$k]['stopnum']=Db::table('pk_stopcarrecord')->where(['mg_id'=>$v['mg_id']])->where('out_time=0')->count();
//                $data[$k]['stopnums']=Db::table('pk_stopcarrecord')->where(['mg_id'=>$v['mg_id']])->count();
                $data[$k]['money']=Db::table('pk_manager')->where(['mg_id'=>$v['mg_id']])->value('price');
            }
            $this->assign(['data'=>$data,'manager_number'=>$manager_number,'people_number'=> $people_number,'mg_name'=>$mg_name]);
            return $this->fetch();
        }else{
            //用户数量
            $data['name']=DB::table('pk_manager')->where(['mg_id'=>$mg_id])->value('name');
            $num1=DB::table('pk_carpos')->where(['mg_id'=>$mg_id])->where('uid','neq',0)->group('uid')->count('uid');
            $num2=DB::table('pk_carpos')->where(['mg_id'=>$mg_id])->where('uuid','neq',0)->group('uuid')->count('uuid');
            $num3=DB::table('pk_carpos')->where(['mg_id'=>$mg_id])->where('uuuid','neq',0)->group('uuuid')->count('uuuid');
            $num4=DB::table('pk_stopcarrecord')->where(['mg_id'=>$mg_id,'status'=>4])->group('uid')->count('uid');
            $data['unum']=$num1+$num2+$num3+$num4;
//            $data['unum']=DB::table('pk_stopcarrecord')->where(['mg_id'=>$mg_id])->group('uid')->count('uid');
//            $data['stopnum']=Db::table('pk_stopcarrecord')->where(['mg_id'=>$mg_id])->where('out_time=0')->count();
//            $data['stopnums']=Db::table('pk_stopcarrecord')->where(['mg_id'=>$mg_id])->count();
            $data['money']=Db::table('pk_manager')->where(['mg_id'=>$mg_id])->value('price');
            $this->assign(['data'=>$data,'mg_name'=>$mg_name]);
            return $this->fetch();
        }
    }
}
