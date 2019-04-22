<?php

namespace app\admin\model;

use think\Db;
use think\Model;

class Carcharge extends Model
{
    protected $table='pk_revenue';
    public function getlist($data){
        (isset($data['plate']) && !empty($data['plate'])) ? $where1['u.plate'] = ['like', '%' . $data['plate'] . '%']:$where1=[];
        (isset($data['reason']) && !empty($data['reason'])) ? $where2['r.reason'] = ['like', '%' . $data['reason'] . '%']:$where2=[];
        /*$data['mg_name']=='admin'?$where4['r.mg_id']=$data['manager']:$where4['r.mg_id']=$data['mg_id'];*/
        if($data['mg_name']=='admin' && $data['manager']===''){
            $where4=[];
        }else if($data['mg_name']=='admin' && $data['manager']!==''){
            if(is_array($data['manager'])){
                $data['manager']=implode(',',$data['manager']);
                $where4['r.mg_id']=array('in',$data['manager']);
            }else{
                $where4['r.mg_id']=$data['manager'];
            }
            
        }else if($data['mg_name']!='admin'){
            $where4['r.mg_id']=$data['mg_id'];
        }
//        $wheres1=['r.type'=>['=',1],
//                   'r.receiptsId'=>['=','m.mg_id']];
//        $wheres2=['r.type'=>['=',2],
//                  'r.receiptsId'=>['=','u.uid']];
        $page=$this::alias('r')
            ->join('__USER__ u','u.uid=r.uid','left')
            ->where($where1)->where($where2)->where($where4)
            ->field('r.*,u.plate')->paginate(25,false,['query'=>$data])->each(function($item,$type){

                if($item['type']==1){
                   $item['names']=Db::table('pk_manager')->where('mg_id',$item['receiptsId'])->value('name');
                }else{
                    $item['names']=Db::table('pk_user')->where('uid',$item['receiptsId'])->value('username');
                }

            });
        //获得分页的页码列表信息 并 传递给模版：
         $pagelist = $page -> render();
         return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }
}
