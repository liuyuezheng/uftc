<?php

namespace app\admin\model;

use think\Model;

class Tixian extends Model
{
	protected $table='pk_tixian';
	public function getlist($data){
		(isset($data['status']) && !empty($data['status'])) ? $where1['r.status'] = ['like', '%' . $data['status'] . '%']:$where1=[];
		$data['start_time']==''?$where3=[]:$where3['duration']=array('gt',$data['start_time']);
         $data['end_time']==''?$where4=[]:$where4['duration']=array('lt',$data['end_time']);
		 $data['mg_name']=='admin'?$where5=[]:$where5['r.mg_id']=$data['mg_id'];
		 if($data['type']==1){
	    	//表示停车场收入
	    	if(!empty($data['province']) && !empty($data['city']) && !empty($data['area'])){
	    		$page=$this::alias('r')
	    		->join('__MANAGER__ m','r.mg_id=m.mg_id','left')
	    		->where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])
	    		->where(['r.type'=>$data['type']])->where($where1)->where($where5)->where($where3)->where($where4)
	    		->order('r.id desc')
	    		->field('r.*,m.name,m.moneybag')->paginate(25,false,['query'=>$data]);
	    	}else if(!empty($data['province']) && !empty($data['city']) && empty($data['area'])){
	    		$page=$this::alias('r')
	    		->join('__MANAGER__ m','r.mg_id=m.mg_id','left')
	    		->where(['province'=>$data['province'],'city'=>$data['city']])
	    		->where(['r.type'=>$data['type']])->where($where1)->where($where5)->where($where3)->where($where4)
	    		->order('r.id desc')
	    		->field('r.*,m.name,m.moneybag')->paginate(25,false,['query'=>$data]);
	    	}else if(!empty($data['province']) && empty($data['city']) && empty($data['area'])){
	    		$page=$this::alias('r')
	    		->join('__MANAGER__ m','r.mg_id=m.mg_id','left')
	    		->where(['province'=>$data['province']])
	    		->where(['r.type'=>$data['type']])->where($where1)->where($where5)->where($where3)->where($where4)
	    		->order('r.id desc')
	    		->field('r.*,m.name,m.moneybag')->paginate(25,false,['query'=>$data]);
	    	}else if(empty($data['province']) && empty($data['city']) && empty($data['area'])){
	    		$page=$this::alias('r')
	    		->join('__MANAGER__ m','r.mg_id=m.mg_id','left')
	    		->where(['r.type'=>$data['type']])->where($where1)->where($where5)->where($where3)->where($where4)
	    		->order('r.id desc')
	    		->field('r.*,m.name,m.moneybag')->paginate(25,false,['query'=>$data]);
	    	}
	    	
	    }else{
	    	//用户收入
	    	$page=$this::alias('r')
	    		->join('__USER__ u','r.uid=u.uid','left')
	    		->where(['r.type'=>$data['type']/*,'u.type'=>1*/])->where($where1)->where($where3)->where($where4)
	    		->order('r.id desc')
	    		->field('r.*,u.username,u.moneybag')->paginate(25,false,['query'=>$data]);
	    }

	    //获得分页的页码列表信息 并 传递给模版：
	     $pagelist = $page -> render();
	     return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
	}

	public function getwlist($data){
		$page=$this::alias('t')
		->join("__MANAGER__ m",'m.mg_id=t.mg_id','left')
		->where(['t.mg_id'=>$data['mg_id']])
		->order('t.id desc')
		->field('t.*,m.name,m.moneybag')->paginate(25,false,['query'=>$data]);
		$pagelist=$page->render();
		return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
	}
}
