<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class Historycarpos extends Model
{
    protected $table = 'pk_historycarpos';

    public function getList($data){
        if(isset($data['number']) && !empty($data['number'])){
            $wherea1['number'] = ['like', '%' . $data['number'] . '%'] ;
            $wherea3['number'] = ['like', '%' . $data['number'] . '%'];
        }else{
            $wherea1=[];
            $wherea3=[];
        };
        (isset($data['types']) && !empty($data['types'])) ? $where2['types'] = $data['types']:$where2=[];
        //(isset($data['manager']) && !empty($data['manager'])) ? $where3['mg_id'] = $data['manager'] : $where3=[];
        if($data['mg_name']=='admin' && $data['manager']===''){
            $where4=[];
        }else if($data['mg_name']=='admin' && $data['manager']!==''){
            if(is_array($data['manager'])){
                $data['manager']=implode(',',$data['manager']);
                $wherea1['mg_id']=['in',$data['manager']];
                $wherea3['mg_id']=['in',$data['manager']];
            }else{
                $wherea1['mg_id']=['eq',$data['manager']];
                $wherea3['mg_id']=['eq',$data['manager']];
            }
        }else if($data['mg_name']!='admin'){
            $wherea1['mg_id']=['eq',$data['mg_id']];
            $wherea3['mg_id']=['eq',$data['mg_id']];
        }
//        (isset($data['number']) && !empty($data['number'])) ? $where1['number'] = ['like', '%' . $data['number'] . '%']:$where1=[];
//        (isset($data['types']) && !empty($data['types'])) ? $where2['types'] = ['like', '%' . $data['types'] . '%']:$where2=[];
//        //(isset($data['manager']) && !empty($data['manager'])) ? $where3['mg_id'] = $data['manager'] : $where3=[];
//        if($data['mg_name']=='admin' && $data['manager']===''){
//            $where4=[];
//        }else if($data['mg_name']=='admin' && $data['manager']!==''){
//            if(is_array($data['manager'])){
//                $data['manager']=implode(',',$data['manager']);
//                $where4['mg_id']=array('in',$data['manager']);
//            }else{
//                $where4['mg_id']=$data['manager'];
//            }
//        }else if($data['mg_name']!='admin'){
//            $where4['mg_id']=$data['mg_id'];
//        }
        $time=time();
        if(isset($where2['types']) && !empty($where2['types'])){
            if($where2['types']==2){
                $wherea1['types']=['eq',2];
                $wherea1['style']=['eq',1];
                $wherea1['end_time']=['<',$time];

            }else{
                $wherea1['types']=['eq',3];
                $wherea1['style']=['eq',1];
                $wherea1['end_time1']=['<',$time];

            }
            $sql=$this::order('id desc')->where($wherea1);
        }else{

            $wherea1['types']=['eq',2];
            $wherea1['style']=['eq',1];
            $wherea1['end_time']=['<',$time];
            //共享

            $wherea3['types']=['eq',3];
            $wherea3['style']=['eq',1];
            $wherea3['end_time1']=['<',$time];


            $sql=$this::order('id desc')->where($wherea1)
                ->whereOr(function ($query) use ($wherea3) {
                    $query->where($wherea3);
                });
        }
        //出租
//        $wherea1=[
//            'style'=>['eq',1],
//            'end_time'=>['<',$time],
//        ];
//        //共享
//        $wherea2=[
//            'style'=>['eq',1],
//            'end_time1'=>['<',$time],
//        ];
        $page = $sql->paginate(15,false,['query'=>$data])->each(function($item,$key){
                            $id=$item['id'];
            $owner=Db::table('pk_historycarpos')->where(['id'=>$id])->field('owner,uid,uuid,uuuid,types,start_time,end_time,start_time1,end_time1,charge,charge1,mg_id')->find();
                /* 2:出租车位 3：共享车位,4临时车位*/
            if($owner['owner']==1){
                $item['haver']=$owner['mg_id'].'_物业';
                if($owner['uuuid']!=0 && $owner['uuid']!=0 && $owner['types']==3){
                    //共享
                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
                    $item['price']=$owner['charge1'];
                }elseif ($owner['uuuid']==0 && $owner['uuid']!=0 && $owner['types']==3){
                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['rentname']='无';
                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
                    $item['price']=$owner['charge1'];
                }else if($owner['uuid']!=0 && $owner['uuuid']==0 && $owner['types']==2){
//                    出租

                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['gongxiang']='';
                    $item['start']=date('Y-m-d',$owner['start_time']);
                    $item['end']=date('Y-m-d',$owner['end_time']);
                    $item['price']=$owner['charge'];
                }elseif($owner['uuid']==0 && $owner['uuuid']==0 && $owner['types']==2){
                    $item['rentname']='无';
//                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['gongxiang']='';
                    $item['start']=date('Y-m-d',$owner['start_time']);
                    $item['end']=date('Y-m-d',$owner['end_time']);
                    $item['price']=$owner['charge'];
                } elseif($owner['uuid']!=0 && $owner['uuuid']==0 && $owner['types']==4){
//                    $item['haver']='物业';
                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['start']=date('Y-m-d H:i',$owner['start_time']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time']);
                    $item['price']=$owner['charge'];
                }
            }
else{
                //个人车位
                $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uid']])->value('username');
                if($owner['types']==3 && $owner['uuuid']!=0 && $owner['uuid']==0){

                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
                    //此时表示直接共享车位
//                    $item['chuzu']='';
//                    $item['gongxiang']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
                    $item['price']=$owner['charge1'];
                }else if($owner['types']==3 && $owner['uuuid']==0 && $owner['uuid']==0){
                    //表示直接共享车位，无人租用过期
//                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['rentname']='无';
//                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['gongxiang']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
                    $item['price']=$owner['charge1'];
                }else if($owner['types']==3 && $owner['uuuid']!=0 && $owner['uuid']!=0){
                    //此时表示出租后再共享
                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
//                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['gongxiang']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
                    $item['price']=$owner['charge1'];
                }elseif($owner['types']==3 && $owner['uuuid']==0 && $owner['uuid']!=0){
//                    此时表示出租后再共享，无人租用过期
//                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['rentname']='无';
//                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['gongxiang']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
                    $item['price']=$owner['charge1'];
                } elseif($owner['types']==2 && $owner['uuid']!=0 && $owner['uuuid']==0){
                    //出租,车位过期
                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uid']])->value('username');
                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['start']=date('Y-m-d',$owner['start_time']);
                    $item['end']=date('Y-m-d',$owner['end_time']);
                    $item['price']=$owner['charge'];
                }elseif($owner['types']==2 && $owner['uuid']==0 && $owner['uuuid']==0){
                    //出租,无人租用车位过期
                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uid']])->value('username');
                    $item['rentname']='无';
//                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['start']=date('Y-m-d',$owner['start_time']);
                    $item['end']=date('Y-m-d',$owner['end_time']);
                    $item['price']=$owner['charge'];
                }   else if($owner['types']==4 && $owner['uuid']!=0 && $owner['uuuid']==0){
                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uid']])->value('username');
                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['start']=date('Y-m-d H:i',$owner['start_time']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time']);
                    $item['price']=$owner['charge'];
                }
            }
            });
//            ->each(function($item,$key){

//        });
//        ;
        //获得分页的页码列表信息 并 传递给模版：
        $pagelist = $page -> render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }
//    public function getList($data){
//        (isset($data['number']) && !empty($data['number'])) ? $where1['number'] = ['like', '%' . $data['number'] . '%']:$where1=[];
//        (isset($data['types']) && !empty($data['types'])) ? $where2['types'] = ['like', '%' . $data['types'] . '%']:$where2=[];
//        //(isset($data['manager']) && !empty($data['manager'])) ? $where3['mg_id'] = $data['manager'] : $where3=[];
//        if($data['mg_name']=='admin' && $data['manager']===''){
//            $where4=[];
//        }else if($data['mg_name']=='admin' && $data['manager']!==''){
//            if(is_array($data['manager'])){
//                $data['manager']=implode(',',$data['manager']);
//                $where4['mg_id']=array('in',$data['manager']);
//            }else{
//                $where4['mg_id']=$data['manager'];
//            }
//        }else if($data['mg_name']!='admin'){
//            $where4['mg_id']=$data['mg_id'];
//        }
//        $page = $this::order('id desc')->where($where1)->where($where2)->where($where4)->where(['style'=>1])->paginate(50,false,['query'=>$data])->each(function($item,$key){
//            $id=$item['id'];
//            $owner=Db::table('pk_historycarpos')->where(['id'=>$id])->field('owner,uid,uuid,uuuid,types,start_time,end_time,start_time1,end_time1')->find();
//            /* 2:出租车位 3：共享车位,4临时车位*/
//            if($owner['owner']==1){
//                $item['haver']='物业';
//                if($owner['uuuid']!=0 && $owner['uuid']!=0 && $owner['types']==3){
//                    //共享
//                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
//                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
//                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
//                }else if($owner['uuid']!=0 && $owner['uuuid']==0 && $owner['types']==2){
////                    出租
//
//                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
////                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
////                    $item['gongxiang']='';
//                    $item['start']=date('Y-m-d',$owner['start_time']);
//                    $item['end']=date('Y-m-d',$owner['end_time']);
//                }elseif($owner['uuid']!=0 && $owner['uuuid']==0 && $owner['types']==4){
////                    $item['haver']='物业';
//                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['start']=date('Y-m-d H:i',$owner['start_time']);
//                    $item['end']=date('Y-m-d H:i',$owner['end_time']);
//                }
//            }else{
//
//                if($owner['types']==3 && $owner['uuuid']!=0 && $owner['uuid']==0){
////                    $list=Db::table('pk_user')->where(['uid'=>$owner['uid']])->field('username,sex');
////                    if($list['sex']==2){
////                        $item['haver']=mb_substr($list['username'],0,1,'utf-8')."女士";
////                    }else{
////                        $item['haver']=mb_substr($list['username'],0,1,'utf-8')."先生";
////                    }
//                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uid']])->value('username');
//                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
//                    //此时表示直接共享车位
////                    $item['chuzu']='';
////                    $item['gongxiang']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
//                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
//                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
//                }else if($owner['types']==3 && $owner['uuuid']!=0 && $owner['uuid']!=0){
//                    //此时表示出租后再共享
//                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
////                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
////                    $item['gongxiang']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
//                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
//                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
//                }else if($owner['types']==2 && $owner['uuid']!=0 && $owner['uuuid']==0){
//                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uid']])->value('username');
//                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
////                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['start']=date('Y-m-d',$owner['start_time']);
//                    $item['end']=date('Y-m-d',$owner['end_time']);
//                }else if($owner['types']==4 && $owner['uuid']!=0 && $owner['uuuid']==0){
//                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uid']])->value('username');
//                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
////                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['start']=date('Y-m-d H:i',$owner['start_time']);
//                    $item['end']=date('Y-m-d H:i',$owner['end_time']);
//                }
//            }
//        });
//        //获得分页的页码列表信息 并 传递给模版：
//        $pagelist = $page -> render();
//        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
//    }
}
