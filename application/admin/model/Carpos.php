<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class Carpos extends Model
{
     protected $table = 'pk_carpos';
    public function getList($data){
        (isset($data['number']) && !empty($data['number'])) ? $where1['number'] = ['like', '%' . $data['number'] . '%']:$where1=[];
        (isset($data['type']) && !empty($data['type'])) ? $where2['type'] = ['like', '%' . $data['type'] . '%']:$where2=[];
        //(isset($data['manager']) && !empty($data['manager'])) ? $where3['mg_id'] = $data['manager'] : $where3=[];
        $data['mg_name']=='admin'?$where4['mg_id']=$data['manager']:$where4['mg_id']=$data['mg_id'];
        $page = $this::order('id desc')->where($where1)->where($where2)->where($where4)->where(['style'=>1])->paginate(15,false,['query'=>$data])->each(function($item,$key){
            $id=$item['id'];
            $owner=Db::table('pk_carpos')->where(['id'=>$id])->field('owner,uid')->find();
            if($owner['owner']==1){
                $item['haver']='物业';
            }else{
                $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uid']])->value('username');
            }
        });
        //获得分页的页码列表信息 并 传递给模版：
        $pagelist = $page -> render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }

    public function getslist($data){
        (isset($data['username']) && !empty($data['username'])) ? $where1['username'] = ['like', '%' . $data['username'] . '%']:$where1=[];
        (isset($data['phone']) && !empty($data['phone'])) ? $where2['phone'] = ['like', '%' . $data['phone'] . '%']:$where2=[];
        $where3['c.style']=['in','2,3'];
        if($data['mg_name']=='admin' && $data['manager']===''){
            $where4=[];
        }else if($data['mg_name']=='admin' && $data['manager']!==''){
            $where4['c.mg_id']=$data['manager'];
        }else if($data['mg_name']!='admin'){
            $where4['c.mg_id']=$data['mg_id'];
        }
        $page=$this::alias('c')
        ->join('__USER__ u','u.uid=c.uid','left')
        ->where($where3)
        ->where($where1)->where($where2)->where($where4)
        ->field('u.username,u.plate,u.phone,c.number,c.logo,c.style,c.id')
        ->paginate(25,false,['query'=>$data]);
        $pagelist=$page->render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }
}
