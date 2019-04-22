<?php

namespace app\admin\model;

use think\Db;
use think\Model;

class Money extends Model
{
	protected $table='pk_revenue';
	public function getlist($data){
        (isset($data['type']) && !empty($data['type'])) ? $where1['type'] = $data['type']:$where1=[];
//		$where1['type']=$data['type'];

         //$data['mg_name']=='admin'?$where4['r.mg_id']=$data['manager']:$where4['r.mg_id']=$data['mg_id'];
         /*$where2['r.mg_id']=array('in',implode(",",$data['mg_ids']));*/
//         if($data['mg_ids']!==''){
//         	$where2['r.mg_id']=array('in',implode(",",$data['mg_ids']));
//         }else{
//         	$where2=[];
//         }
         $data['start_time']==''?$where3=[]:$where3['r.duration']=array('gt',$data['start_time']);
         $data['end_time']==''?$where4=[]:$where4['r.duration']=array('lt',$data['end_time']);
         if($data['mg_name']=='admin'){
         	$page=$this::alias('r')
//	    		->join('__USER__ u','u.uid=r.oid','left')
//	    		->join('__MANAGER__ m','m.mg_id=r.mg_id','left')
	    		->where($where1)
//	    		->where($where2)
	    		->where($where3)
                ->where($where4)
                ->order('duration desc')
                ->paginate(15,false,['query'=>$data])->each(function($item,$key){
	    		    if($item['type']==1){
                       $item['names']=Db::table('pk_manager')->where('mg_id',$item['receiptsId'])->value('name');
                    }else{
                        $item['names']=Db::table('pk_user')->where('uid',$item['receiptsId'])->value('username');
                    }
                });
         }else{
         	$page=$this::alias('r')
//	    		->join('__USER__ u','u.uid=r.oid','left')
//	    		->join('__MANAGER__ m','m.mg_id=r.mg_id','left')
	    		->where($where1)
	    		->where($where3)->where($where4)
	    		->where('mg_id',$data['mg_id'])
                ->order('duration desc')
                ->paginate(15,false,['query'=>$data])->each(function($item,$key){
                    if($item['type']==2){
                        $item['names']=Db::table('pk_user')->where('uid',$item['receiptsId'])->value('username');
                    }else{
                        $item['names']=Db::table('pk_manager')->where('mg_id',$item['mg_id'])->value('name');
                    }
                });
         }
	    
	    //$data['start_time']=date('Y-m-d H:i:s',$data['start_time']);
	    //获得分页的页码列表信息 并 传递给模版：
	     $pagelist = $page -> render();
	     return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
	}
}
