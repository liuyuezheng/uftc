<?php

namespace app\admin\model;

use think\Model;

class Device extends Model
{
    protected $table = 'pk_device';
    public function getList($data){
        (isset($data['serialno']) && !empty($data['serialno'])) ? $where1['d.serialno'] = ['like', '%' . $data['serialno'] . '%']:$where1=[];
        if($data['mg_name']=='admin' && $data['manager']===''){
            $where4=[];
        }else if($data['mg_name']=='admin' && $data['manager']!==''){
            if(is_array($data['manager'])){
                $data['manager']=implode(',',$data['manager']);
                $where4['d.mg_id']=array('in',$data['manager']);
            }else{
                $where4['d.mg_id']=$data['manager'];
            }
        }else if($data['mg_name']!='admin'){
            $where4['d.mg_id']=$data['mg_id'];
        }
        $page = $this::alias('d')
        	->join('__MANAGER__ m','d.mg_id=m.mg_id','left')
        	->field('d.*,m.mg_name')
        	->order('d.id desc')
        	->where($where1)->where($where4)->paginate(25,false,['query'=>$data]);
        //获得分页的页码列表信息 并 传递给模版：
        $pagelist = $page -> render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }
}
