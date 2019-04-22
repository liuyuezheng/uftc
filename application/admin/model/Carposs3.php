<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class Carposs extends Model
{
     protected $table = 'pk_carpos';

     public function getList($data){
        (isset($data['number']) && !empty($data['number'])) ? $where1['number'] = ['like', '%' . $data['number'] . '%']:$where1=[];
        (isset($data['type']) && !empty($data['type'])) ? $where2['type']= ['like', '%' . $data['type'] . '%']:$where2=[];
        //(isset($data['manager']) && !empty($data['manager'])) ? $where3['mg_id'] = $data['manager'] : $where3=[];
        /*$data['mg_name']=='admin'?$where4['mg_id']=$data['manager']:$where4['mg_id']=$data['mg_id'];*/
        if($data['mg_name']=='admin' && $data['manager']===''){
            $where4=[];
        }else if($data['mg_name']=='admin' && $data['manager']!==''){
            if(is_array($data['manager'])){
                $data['manager']=implode(',',$data['manager']);
                $where4['mg_id']=array('in',$data['manager']);
            }else{
                $where4['mg_id']=$data['manager'];
            }
        }else if($data['mg_name']!='admin'){
            $where4['mg_id']=$data['mg_id'];
        }
//         $time=time();
//         //物业租赁（添加物业车位时直接添加租赁车位）
//         $wherea1=[
//             'type'=>['eq',2],
////             'style'=>['eq',1],
//             'types'=>['eq',2],
//             'owner'=>['eq',1],
//             'number'=>[$where1],
//             ''
////             'type'=>[$where2],
//         ];
//         //物业车位（添加临时车位）
//         $wherea2=[
//             'type'=>['in','1,3,4'],
//             'types'=>['eq',4],
////             'style'=>['eq',1],
//             'owner'=>['eq',1],
//         ];
//         //个人
//         $wherea3=[
//             'type'=>['in','1,2,3,4'],
//             'types'=>['eq',1],
////             'style'=>['eq',1],
//             'owner'=>['eq',2],
//         ];
        $page = $this::order('id desc')->where($where1)->where($where2)->where($where4)
          ->group('mg_id,number')->where(['style'=>1])->paginate(15,false,['query'=>$data])->each(function($item,$key){
            $id=$item['id'];
            $owner=Db::table('pk_carpos')->where(['id'=>$id])->field('owner,uid,mg_id')->find();
            if($owner['owner']==1){
                $item['haver']=$owner['mg_id'].'_物业';
            }else{
                $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uid']])->value('username');
            }
        });
        //获得分页的页码列表信息 并 传递给模版：
        $pagelist = $page -> render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }


    public function getLists($data){
        (isset($data['number']) && !empty($data['number'])) ? $where1 = ['like', '%' . $data['number'] . '%']:$where1=[];
        (isset($data['types']) && !empty($data['types'])) ? $where2 = $data['types']:$where2=[];
        //(isset($data['manager']) && !empty($data['manager'])) ? $where3['mg_id'] = $data['manager'] : $where3=[];
       if($data['mg_name']=='admin' && $data['manager']===''){
            $where4=[];
        }else if($data['mg_name']=='admin' && $data['manager']!==''){
            if(is_array($data['manager'])){
                $data['manager']=implode(',',$data['manager']);
                $where4['mg_id']=array('in',$data['manager']);
            }else{
                $where4['mg_id']=$data['manager'];
            }
        }else if($data['mg_name']!='admin'){
            $where4['mg_id']=$data['mg_id'];
        }
//        $where5['types']=array('in','2,3');
        $time=time();
        if(isset($where2) && !empty($where2)){
            if($where2==2){
//出租
                $wherea1=[
                    'types'=>['eq',2],
                    'style'=>['eq',1],
                    'end_time'=>['>',$time],
                    'number'=>$where1,
                ];

            }else{
                //共享
                $wherea1=[
                    'types'=>['eq',3],
                    'style'=>['eq',1],
                    'end_time1'=>['>',$time],
                    'number'=>$where1,
                ];
            }
            $sql=$this::order('id desc')->where($where4)->where($wherea1);
        }else{
            $wherea1=[
                'types'=>['eq',2],
                'style'=>['eq',1],
                'end_time'=>['>',$time],
                'number'=>$where1,
            ];
            //共享
            $wherea3=[
                'types'=>['eq',3],
                'style'=>['eq',1],
                'end_time1'=>['>',$time],
                'number'=>$where1,
            ];
            $sql=$this::order('id desc')->where($where4)->where($wherea1)
                ->whereOr(function ($query) use ($wherea3) {
                    $query->where($wherea3);
                });
        }


        $page = $sql
//          ->fetchSql()->select();
        ->paginate(15,false,['query'=>$data])
            ->each(function($item,$key){
            $id=$item['id'];
            $owner=Db::table('pk_carpos')->where(['id'=>$id])->field('owner,uid,uuid,uuuid,types,charge,charge1,mg_id,start_time,end_time,start_time1,end_time1')->find();
            if($owner['owner']==1){
                $item['haver']=$owner['mg_id'].'_物业';
                if($owner['types']==3 && $owner['uuuid']!=0 && $owner['uuid']!=0){
                    //出租发布共享，有人租用
                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['gongxiang']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
                    $item['plate']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('plate');
                    $item['price']=$owner['charge1'];
                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
                }else if($owner['types']==3 && $owner['uuuid']==0 && $owner['uuid']!=0){
                    //出租发布共享，无人租用
                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['rentname']='无';
                    $item['plate']='无';
                    $item['price']=$owner['charge1'];
                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
                }else if($owner['types']==2 && $owner['uuid']!=0 && $owner['uuuid']==0){
                    //出租
                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['plate']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('plate');
                    $item['price']=$owner['charge'];
                    $item['start']=date('Y-m-d H:i',$owner['start_time']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time']);
                }else if($owner['types']==2 && $owner['uuid']==0 && $owner['uuuid']==0){
                    //出租，无人租用
                    $item['rentname']='无';
                    $item['plate']='无';
                    $item['price']=$owner['charge'];
                    $item['start']=date('Y-m-d',$owner['start_time']);
                    $item['end']=date('Y-m-d',$owner['end_time']);
                }
            }else{
                $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uid']])->value('username');
                if($owner['types']==3 && $owner['uuuid']!=0 && $owner['uuid']==0){
                    //此时表示直接共享车位
//                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
//                    $item['gongxiang']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
                    $item['plate']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('plate');
                    $item['price']=$owner['charge1'];
                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
                }elseif($owner['types']==3 && $owner['uuuid']==0 && $owner['uuid']==0){
                    //此时表示直接共享车位,无人租用
//                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
//                    $item['gongxiang']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['rentname']='无';
                    $item['plate']='无';
                    $item['price']=$owner['charge1'];
                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
                }else if($owner['types']==3 && $owner['uuuid']!=0 && $owner['uuid']!=0){
                    //此时表示出租后再共享
//                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['gongxiang']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
                    $item['plate']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('plate');
                    $item['price']=$owner['charge1'];
                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
                }else if($owner['types']==3 && $owner['uuuid']==0 && $owner['uuid']!=0){
                    //此时表示出租后再共享，无人租用
//                    $item['chuzu']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
//                    $item['gongxiang']=Db::table('pk_user')->where(['uid'=>$owner['uuuid']])->value('username');
                    $item['haver']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['rentname']='无';
                    $item['plate']='无';
                    $item['price']=$owner['charge1'];
                    $item['start']=date('Y-m-d H:i',$owner['start_time1']);
                    $item['end']=date('Y-m-d H:i',$owner['end_time1']);
                }else if($owner['types']==2 && $owner['uuid']!=0){
//                    出租
                    $item['rentname']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('username');
                    $item['plate']=Db::table('pk_user')->where(['uid'=>$owner['uuid']])->value('plate');
                    $item['price']=$owner['charge'];
                    $item['start']=date('Y-m-d',$owner['start_time']);
                    $item['end']=date('Y-m-d',$owner['end_time']);
                }else if($owner['types']==2 && $owner['uuid']==0){
//                    出租，无人租用
                    $item['rentname']='无';
                    $item['plate']='无';
                    $item['price']=$owner['charge'];
                    $item['start']=date('Y-m-d',$owner['start_time']);
                    $item['end']=date('Y-m-d',$owner['end_time']);
                }
            }
        });
//        获得分页的页码列表信息 并 传递给模版：
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
            if(is_array($data['manager'])){
                $data['manager']=implode(',',$data['manager']);
                $where4['c.mg_id']=array('in',$data['manager']);
            }else{
                $where4['c.mg_id']=$data['manager'];
            }
        }else if($data['mg_name']!='admin'){
            $where4['c.mg_id']=$data['mg_id'];
        }
        
        $page=$this::alias('c')
        ->join('__USER__ u','u.uid=c.uid','left')
            ->join('__MANAGER__ m','m.mg_id=c.mg_id','left')
        ->where($where3)
        ->where($where1)->where($where2)->where($where4)
        ->field('u.username,u.plate,u.phone,c.number,c.logo,c.style,c.id,c.create_time,c.update_time,m.name')
        ->order('c.create_time,c.style desc')
        ->paginate(10,false,['query'=>$data]);
        $pagelist=$page->render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }
}
