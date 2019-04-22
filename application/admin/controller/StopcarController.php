<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Session;
use think\Db;
use app\api\controller\IndexController;

class StopcarController extends Controller
{
    public function index(Request $request)
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
        $stop_model=new \app\admin\model\Stopcar();
        $shuju=$stop_model->getList($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
//        $data['mg_name']=$request->param('mg_name');
//        $data['mg_id']=$request->param('mg_id');
//        $stop_model=new \app\admin\model\Stopcar();
//        $shuju=$stop_model->getList($data);
//        return json_encode($shuju);
    }
    public function listIndex(Request $request){
        $data['plate']=$request->param('plate');
        $data['province']=$request->param('province');
        $data['city']=$request->param('city');
        $data['area']=$request->param('area');

        $data['manager']=$request->param('manager');
        if(!empty($data['province']) && !empty($data['city']) && !empty($data['area'])){
            $manager=Db::table('pk_manager')->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->column('mg_id');
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
        $stopcar_model=new \app\admin\model\Stopcar();
        $shuju=$stopcar_model->getlists($data);
        $this->assign('shuju',$shuju);
        return $this -> fetch();
    }

    public function add(Request $request){
        if($request->isPost()){
            $mg_name=Session::get('mg_name');
            $mg_id=Session::get('mg_id');
            $data=$request->post();
            $data['timelong']=strtotime($data['out_time'])-strtotime($data['in_time']);
            if($mg_name=='admin'){
                $shuju=[
                    'plate'=>$data['plate'],
                    'in_time'=>strtotime($data['in_time']),
                    'out_time'=>strtotime($data['out_time']),
                    'timelong'=>$data['timelong'],
                    'mg_id'=>$data['mg_id'],
                    'number'=>$data['cid'],
                ];
                $res=Db::table('pk_stopcarrecord')->insert($shuju);
            }else{
                $shuju=[
                    'plate'=>$data['plate'],
                    'in_time'=>strtotime($data['in_time']),
                    'out_time'=>strtotime($data['out_time']),
                    'timelong'=>$data['timelong'],
                    'mg_id'=>Session::get('mg_id'),
                    'number'=>$data['cid'],
                ];
                $res1=Db::table('pk_stopcarrecord')->insert($shuju);
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
            $this->assign('data',$data);
            return $this->fetch();
        }
    }
}
