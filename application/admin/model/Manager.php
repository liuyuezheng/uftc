<?php

namespace app\admin\model;

use think\Model;

class Manager extends Model
{
	protected $table='pk_manager';
    public static function invoke()
    {
        $id = request()->param('mg_id');
        return self::get($id); //返回的model对象可以被控制器方法的$ctask接收
    }

    public function getlist($data){
    	(isset($data['name']) && !empty($data['name'])) ? $where1['name'] = ['like', '%' . $data['name'] . '%']:$where1=[];
        (isset($data['manager']) && !empty($data['manager'])) ? $where3['mg_id'] = $data['manager'] : $where3=[];
    	$where2['mg_name']=array('neq','admin');
        /*$data['mg_name']=='admin'?$where4=['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']]:$where4['mg_id']=$data['mg_id'];*/
        if($data['mg_name']=='admin' && !empty($data['province']) &&!empty($data['city']) && !empty($data['area'])){
            $where4=['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']];
        }else if($data['mg_name']=='admin' && !empty($data['province']) &&!empty($data['city']) && empty($data['area'])){
            $where4=['province'=>$data['province'],'city'=>$data['city']];
        }else if($data['mg_name']=='admin' && !empty($data['province']) &&empty($data['city']) && empty($data['area'])){
            $where4=['province'=>$data['province']];
        }else if($data['mg_name']=='admin' && empty($data['province']) &&empty($data['city']) && empty($data['area'])){
            $where4=[];
        }else if($data['mg_name']!='admin'){
            $where4['mg_id']=$data['mg_id'];
        }

        $page = $this::order('mg_id desc')->where($where1)->where($where2)->where($where3)->where($where4)->paginate(15,false,['query'=>$data]);
        //获得分页的页码列表信息 并 传递给模版：
        $pagelist = $page -> render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }

    public function add($data){
    	if(!empty($data['mg_id'])){
    		$data1  = $data['mg_id'];
    		unset($data['mg_id']);unset($data['/admin/manager/add_html']);
            $res = $this::where(['mg_id'=>$data1])->update($data);
            if(!empty($res)){
                return 2;
            }
    	}else{
    		unset($data['mg_id']);unset($data['/admin/manager/add_html']);
            $res = $this::insert($data);
            if(!empty($res)){
                return 1;
            }
    	}
    }

     //获取修改内容
    public function edit($id){
        $res = $this::where(['mg_id'=>$id])->find();
        if(!empty($res)){
            return $res;
        }
    }
}
