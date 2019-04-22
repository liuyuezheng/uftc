<?php

namespace app\admin\model;

use think\Db;
use think\Model;

class Money extends Model
{
	protected $table='pk_revenue';
	public function getlist($data){
        (isset($data['type']) && !empty($data['type'])) ? $where1['r.type'] = $data['type']:$where1=[];
//		$where1['type']=$data['type'];
//        (isset($data['province']) && !empty($data['province'])) ? $where1['province'] = $data['province']:$where1=[];
//        (isset($data['city']) && !empty($data['city'])) ? $where1['city'] = $data['province']:$where1=[];
//        (isset($data['province']) && !empty($data['province'])) ? $where1['province'] = $data['province']:$where1=[];
//        $data['province']==''?$where3=[]:$where3['m.province']=array('gt',$data['start_time']);
//        $data['city']==''?$where3=[]:$where3['r.duration']=array('gt',$data['start_time']);
//        $data['area']==''?$where3=[]:$where3['r.duration']=array('gt',$data['start_time']);
        $data['start_time']==''?$where3=[]:$where3['r.duration']=array('gt',$data['start_time']);
        $data['end_time']==''?$where4=[]:$where4['r.duration']=array('lt',$data['end_time']);
        $data['mg_name']=='admin'?$where5=[]:$where5['r.mg_id']=$data['mg_id'];
        if($data['type']==1){
            //表示停车场收入
            if(!empty($data['province']) && !empty($data['city']) && !empty($data['area'])){
                $page=$this::alias('r')
                    ->join('__MANAGER__ m','r.mg_id=m.mg_id','left')
                    ->where(['m.province'=>$data['province'],'m.city'=>$data['city'],'m.area'=>$data['area']])
                    ->where(['r.type'=>$data['type']])->where($where1)->where($where5)->where($where3)->where($where4)
                    ->order('r.id desc')
                    ->field('r.*,m.name as names')->paginate(25,false,['query'=>$data]);
            }else if(!empty($data['province']) && !empty($data['city']) && empty($data['area'])){
                $page=$this::alias('r')
                    ->join('__MANAGER__ m','r.mg_id=m.mg_id','left')
                    ->where(['m.province'=>$data['province'],'m.city'=>$data['city']])
                  ->where($where1)->where($where5)->where($where3)->where($where4)
                    ->order('r.id desc')
                    ->field('r.*,m.name as names')->paginate(25,false,['query'=>$data]);
            }else if(!empty($data['province']) && empty($data['city']) && empty($data['area'])){
                $page=$this::alias('r')
                    ->join('__MANAGER__ m','r.mg_id=m.mg_id','left')
                    ->where(['m.province'=>$data['province']])
                    ->where($where1)->where($where5)->where($where3)->where($where4)
                    ->order('r.id desc')
                    ->field('r.*,m.name as names')->paginate(25,false,['query'=>$data]);
            }else if(empty($data['province']) && empty($data['city']) && empty($data['area'])){
                $page=$this::alias('r')
                    ->join('__MANAGER__ m','r.mg_id=m.mg_id','left')
                   ->where($where1)->where($where5)->where($where3)->where($where4)
                    ->order('r.id desc')
                    ->field('r.*,m.name as names')->paginate(25,false,['query'=>$data]);
            }

        }else{
            //用户收入
            $page=$this::alias('r')
                ->join('__USER__ u','r.uid=u.uid','left')
               ->where($where1)->where($where3)->where($where4)
                ->order('r.id desc')
                ->field('r.*,u.username as names')->paginate(25,false,['query'=>$data]);
        }

	    //获得分页的页码列表信息 并 传递给模版：
	     $pagelist = $page -> render();
	     return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
	}
}
