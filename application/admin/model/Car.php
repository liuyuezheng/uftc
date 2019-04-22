<?php

namespace app\admin\model;

use think\Model;

class Car extends Model
{
    protected $table = 'pk_cars';
    public function getList($data){
        (isset($data['username']) && !empty($data['username'])) ? $where1['username'] = ['like', '%' . $data['username'] . '%']:$where1=[];
        (isset($data['phone']) && !empty($data['phone'])) ? $where2['car.phone'] = $data['phone'] :$where2=[];
        (isset($data['type']) && !empty($data['type'])) ? $where3['car.type'] = ['like', '%' . $data['type'] . '%']:$where3=[];
        $page = $this::alias('car')
        	->join('__USER__ u','car.uid=u.uid','left')
        	->field('car.*,u.username')
        	->order('car.update_time desc')
        	->where($where1)->where($where2)->where($where3)->paginate(25,false,['query'=>$data]);
        //获得分页的页码列表信息 并 传递给模版：
        $pagelist = $page -> render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }
}
