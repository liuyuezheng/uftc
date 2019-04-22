<?php

namespace app\admin\controller;

use think\cache\driver\Redis;
use think\Controller;
use think\Request;
use app\admin\Model\Device;
use think\Db;
use think\Session;
use app\api\Controller\IndexController;

class DeviceController extends Controller
{

    public function reTest(){
        $redis=new Redis();
        $redis->set('test',"redis缓存后台管理");
        $res=$redis->get('test');
        var_dump($res);
    }
    public function index(Request $request)
    {
        $data['serialno']=$request->param('serialno');
        $data['type']=$request->param('type');
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
        $car_model=new \app\admin\model\Device();
        $shuju=$car_model->getlist($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
    }

    public function add(Request $request){
        if($request->isPost()){
            $data['mg_id'] = $request->post('manager');
            $data['serialno'] = $request->post('serialno');
            $data['type'] = $request->post('type');
            $data['port'] = $request->post('port');
            $province = $request ->post('province');
            $city = $request ->post('city');
            $area = $request ->post('area');
            $res= Db::table('pk_device')->insert($data);
            if(!empty($res)){
                return ['status'=>'success','province'=>$province,'city'=>$city,'area'=>$area];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            return $this->fetch();
        }
    }

    public function edit(Request $request){
        if($request->isPost()){
            $id = $request->param('id');
            $data['mg_id'] = $request->post('manager');
            $data['serialno'] = $request->post('serialno');
            $data['type'] = $request->post('type');
            $data['port'] = $request->post('port');
            $province = $request ->post('province');
            $city = $request ->post('city');
            $area = $request ->post('area');
            $res= Db::table('pk_device')->where(['id'=>$id])->update($data);
            if(!empty($res)){
                return ['status'=>'success','province'=>$province,'city'=>$city,'area'=>$area,'manager'=>$data['mg_id']];
            }else{
                return ['status'=>'failure'];
            }
        }else{
            $id=$request->param('id');
            $data= \app\admin\model\Device::alias('d')
            ->join('__MANAGER__ m','m.mg_id=d.mg_id')
            ->where(['d.id'=>$id])
            ->field('d.*,m.province,m.city,m.area')
            ->find();
            $this->assign('res',$data);
            return $this->fetch();
        }
    }

    public function delete(Request $request){
        $id=$request->param('id');
        $res = Db::table('pk_device')->where(['id'=>$id])->delete();
        if(!empty($res)){
            return ['status'=>'success'];
        }else{
            return ['status'=>'failure'];
        }
    }

    public function startcha(Request $request){
        $id = $request->param('id');
        $chanum = Db::table('pk_device')->where(['id'=>$id])->value('port');
        $index = new \app\api\Controller\IndexController();
        $index -> recplate(2,$chanum);
    }
}
