<?php

namespace app\admin\model;

use think\Model;

class Sensor extends Model
{
    protected $table = 'pk_sensor';
    public function getList($data){
       /* (isset($data['number']) && !empty($data['number'])) ? $where1['number'] = ['like', '%' . $data['number'] . '%']:$where1=[];
        (isset($data['type']) && !empty($data['type'])) ? $where2['type'] = ['like', '%' . $data['type'] . '%']:$where2=[];*/
        //(isset($data['manager']) && !empty($data['manager'])) ? $where3['mg_id'] = $data['manager'] : $where3=[];
        /*$data['mg_name']=='admin'?$where4['mg_id']=$data['manager']:$where4['mg_id']=$data['mg_id'];*/
        if($data['mg_name']=='admin' && $data['manager']===''){
            $where4=[];
        }else if($data['mg_name']=='admin' && $data['manager']!==''){
            if(is_array($data['manager'])){
                $data['manager']=implode(',',$data['manager']);
                $where4['s.mg_id']=array('in',$data['manager']);
            }else{
                $where4['s.mg_id']=$data['manager'];
            }
        }else if($data['mg_name']!='admin'){
            $where4['s.mg_id']=$data['mg_id'];
        }

        $page = $this::alias('s')
        ->join('__MANAGER__ m','s.mg_id=m.mg_id')
        ->join('__CARPOS__ c','c.id=s.pid')
        ->where($where4)->field('s.*,m.name,c.number')->paginate(15,false,['query'=>$data]);
        //获得分页的页码列表信息 并 传递给模版：
        $pagelist = $page -> render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }
}
