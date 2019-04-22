<?php

namespace app\admin\model;

use think\Model;

class Stopcar extends Model
{
    protected $table = 'pk_stopcarrecord';
    public function getList($data){
        (isset($data['username']) && !empty($data['username'])) ? $where1['u.username'] = ['like', '%' . $data['username'] . '%']:$where1=[];
        (isset($data['phone']) && !empty($data['phone'])) ? $where2['u.phone'] = ['like', '%' . $data['phone'] . '%']:$where2=[];
        //(isset($data['manager']) && !empty($data['manager'])) ? $where5['mg_id'] = $data['manager'] : $where5=[];
//        $manager=$data['manager'];
//        $mg_id=$data['mg_id'];
        /*$data['mg_name']=='admin'?$where6="FIND_IN_SET('$manager',mg_id)":$where6="FIND_IN_SET('$mg_id',mg_id)";*/
        if($data['mg_name']=='admin' && $data['manager']===''){
            $where4=[];
        }else if($data['mg_name']=='admin' && $data['manager']!==''){
            if(is_array($data['manager'])){
                $data['manager']=implode(',',$data['manager']);
                $where4['s.mg_id']=array('in',$data['manager']);
            }else{
                $where4['s.mg_id']=$data['manager'];
            }
        }else if($data['mg_name']!='admin'){
            $where4['s.mg_id']=$data['mg_id'];
        }
        /*$page = $this::order('uid desc')->where($where1)->where($where2)->where($where6)->paginate(20,false,['query'=>$data]);*/
//   $where5=[''];
        $page = $this::alias('s')
            ->join('pk_user u','u.uid=s.uid')
            ->join('pk_manager m','m.mg_id=s.mg_id')
            ->where($where1)
            ->where($where2)
            ->where($where4)
            ->where(['s.status'=>4])
            ->group('s.mg_id,s.uid')
            ->field('s.*,u.phone,u.username,m.name')
            ->paginate(15,false,['query'=>$data]);
//            ->each(function($item,$key){
//                $id=$item['id'];
//                $owner=Db::table('pk_stopcarrecord')->alias('c')
//                    ->join('pk_manager m','c.mg_id=m.mg_id')
//                    ->where(['c.id'=>$id])->field('m.name')->find();
//                $item['mg_name']=$owner['name'];
//            });
        //dump($page);
        //获得分页的页码列表信息 并 传递给模版：
        $pagelist = $page -> render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }
    public function getLists($data){
        (isset($data['plate']) && !empty($data['plate'])) ? $where1['s.plate'] = ['like', '%' . $data['plate'] . '%']:$where1=[];
        if($data['mg_name']=='admin' && $data['manager']===''){
            $where4=[];
        }else if($data['mg_name']=='admin' && $data['manager']!==''){
            if(is_array($data['manager'])){
                $data['manager']=implode(',',$data['manager']);
                $where4['s.mg_id']=array('in',$data['manager']);
            }else{
                $where4['s.mg_id']=$data['manager'];
            }

        }else if($data['mg_name']!='admin'){
            $where4['s.mg_id']=$data['mg_id'];
        }

        $page = $this::alias('s')
            ->join('__USER__ u','s.uid=u.uid','left')
            ->field('s.*,u.username')
            ->order('id desc')
            ->where($where1)->where($where4)->paginate(25,false,['query'=>$data]);//dump($page);
        //$page->timelong=time_difference($page->timelong);
        //获得分页的页码列表信息 并 传递给模版：
        $pagelist = $page -> render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }
}
