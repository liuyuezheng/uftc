<?php

namespace app\admin\model;

use think\Model;

class Balance extends Model
{
    protected $table='pk_balance';
	public function getlist($data){
		(isset($data['name']) && !empty($data['name'])) ? $where1['m.name'] = ['like', '%' . $data['name'] . '%']:$where1=[];
	    	$page=$this::alias('b')
	    		->join('__MANAGER__ m','b.mg_id=m.mg_id')
	    		->where(['b.type'=>$data['type']])
	    		->where($where1)
	    		->field('b.*,m.name')->paginate(2);
	    //获得分页的页码列表信息 并 传递给模版：
	     $pagelist = $page -> render();
	     return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
	}
}
