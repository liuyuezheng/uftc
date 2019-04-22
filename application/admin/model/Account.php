<?php

namespace app\admin\model;

use think\Model;
use app\admin\model\Manager;
use app\admin\model\User;

class Account extends Model
{
	public function getlist($data){
		/*(isset($data['status']) && !empty($data['status'])) ? $where1['r.status'] = ['like', '%' . $data['status'] . '%']:$where1=[];*/
		 /*$data['mg_name']=='admin'?$where5=[]:$where5['r.mg_id']=$data['mg_id'];*/
		 if($data['type']==1){
	    	//表示停车场收入

		 	if(!empty($data['province']) && !empty($data['city']) && !empty($data['area'])){
		 		$page=Manager::where(['province'=>$data['province'],'city'=>$data['city'],'area'=>$data['area']])->where('mg_id != 1')
		    		->order('mg_id desc')
		    		->paginate(25,false,['query'=>$data])->each(function($item,$key){
		    			$item['id']=$item['mg_id'];
	        	});
		    }else if(!empty($data['province']) && !empty($data['city']) && empty($data['area'])){
		    	$page=Manager::where(['province'=>$data['province'],'city'=>$data['city']])
		    		->where('mg_id != 1')
		    		->order('mg_id desc')
		    		->paginate(25,false,['query'=>$data])->each(function($item,$key){
		    			$item['id']=$item['mg_id'];
	        	});
		    }else if(!empty($data['province']) && empty($data['city']) && empty($data['area'])){
		    	$page=Manager::where(['province'=>$data['province']])
		    		->where('mg_id != 1')
		    		->order('mg_id desc')
		    		->paginate(25,false,['query'=>$data])->each(function($item,$key){
		    			$item['id']=$item['mg_id'];
	        	});
		    }else if(empty($data['province']) && empty($data['city']) && empty($data['area'])){
		    	$page=Manager::order('mg_id desc')
		    		->where('mg_id != 1')
		    		->paginate(25,false,['query'=>$data])->each(function($item,$key){
		    			$item['id']=$item['mg_id'];
	        	});
		    }

	    }else{
	    	//用户收入
	    	$page=User::order('uid desc')
	    		->paginate(25,false,['query'=>$data])->each(function($item,$key){
	    		$item['id']=$item['uid'];
        	});
	    }

	    //获得分页的页码列表信息 并 传递给模版：
	     $pagelist = $page -> render();
	     return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
	}
}
