<?php

namespace app\admin\model;

use think\Model;

class Recharge extends Model
{
    protected $table='pk_recharges';
    public function getList($data){
    	(isset($data['username']) && !empty($data['username'])) ? $where1['username'] = ['like', '%' . $data['username'] . '%']:$where1=[];
    	 $data['start_time']==''?$where3=[]:$where3['r.dateline']=array('gt',$data['start_time']);
         $data['end_time']==''?$where4=[]:$where4['r.dateline']=array('lt',$data['end_time']);
    	$page=$this::alias('r')
    		->join('__USER__ u','u.uid=r.uid','left')
    		->where($where1)
    		->where($where3)->where($where4)
    		->order('r.id desc')
    		->field('r.*,u.username')->paginate(25,false,['query'=>$data]);
	    $pagelist = $page -> render();
	    return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }
}
