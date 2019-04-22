<?php

namespace app\api\controller;

use app\api\service\TokenService;
use think\Controller;
use think\Request;
use app\api\Model\Carpos;
use app\api\Model\Manager;
use app\api\Model\Historycarpos;
use app\api\Model\Stopcarrecord;
use think\Db;

class CarposController extends BaseController
{
//    public function test(){
//        $arr=['a'=>8,'b'=>9];
//        asort($arr);
//        end($arr);
//        $aa=key($arr);
//        return json_encode(['code'=>1000,'data'=>$aa,'msg'=>'车位信息获取成功']);
//    }
	//车位页面
    public function index()
    {
          $uid=input('uid/d');
        if (!TokenService::checkUserId($uid)) {
            return success(1002, '', '无效token');
        }
        $page = input('page', 1);
        $num =100;//每页显示的条数
        $maxpage=1;
        $start = ($page - 1) * $num;
        $where2['c.uuid']=array('eq',0);
        $where3['c.uuid']=array('neq',0);
        $time=time();
        //个人车位
        $personal=DB::table('pk_carpos')->alias('c')
        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
        ->where(['c.uid'=>$uid,'c.style'=>1,'c.types'=>1,'c.status'=>2])//表示未出租，且审核通过
        ->where($where2)
        ->field('c.id,c.number,c.types,m.name,m.address,m.area,c.end_time,m.longitude,m.latitude')
        ->select();
        $whererent1=[
            'c.uuid'=>['neq',0],
            'c.start_time'=>['>',$time],
            'c.uid'=>['=',$uid],
            'c.style'=>['=',1],
            'c.types'=>['=',2],
            'c.owner'=>['=',2],
        ];
        $whererent2=[
            'c.uuid'=>['neq',0],
            'c.end_time'=>['<',$time],
            'c.uid'=>['=',$uid],
            'c.style'=>['=',1],
            'c.types'=>['=',2],
            'c.owner'=>['=',2],
        ];
//        $whererent=['c.uuid'=>['ne']];
        //车位已出租，不在租用的时间属于个人车位
        $personal2=DB::table('pk_carpos')->alias('c')
            ->join('__MANAGER__ m','c.mg_id=m.mg_id')
            ->where($whererent1)
            ->whereOr(function ($query) use ($whererent2) {
                $query->where($whererent2);
            })
         
            ->group('c.mg_id','c.number')
            ->field('c.id,c.number,c.types,m.name,m.address,m.area,c.end_time,m.longitude,m.latitude')
            ->select();
        //车位已共享，不在共享的时间属于个人车位
        $whererent3=[
            'c.uuid'=>['=',0],
            'c.uuuid'=>['neq',0],
            'c.start_time'=>['>',$time],
            'c.uid'=>['=',$uid],
            'c.style'=>['=',1],
            'c.types'=>['=',3],
            'c.owner'=>['=',2],
        ];
        $whererent4=[
            'c.uuid'=>['=',0],
            'c.uuuid'=>['neq',0],
            'c.end_time'=>['<',$time],
            'c.uid'=>['=',$uid],
            'c.style'=>['=',1],
            'c.types'=>['=',3],
            'c.owner'=>['=',2],
        ];
        $personal3=DB::table('pk_carpos')->alias('c')
            ->join('__MANAGER__ m','c.mg_id=m.mg_id')
            ->where($whererent3)
            ->whereOr(function ($query) use ($whererent4) {
                $query->where($whererent4);
            })
            ->group('c.mg_id','c.number')
            ->field('c.id,c.number,c.types,m.name,m.address,m.area,c.end_time,m.longitude,m.latitude')
            ->select();
        foreach ($personal2 as $k2=>$v2){
            $personal2[$k2]['types']=1;
        }
        foreach ($personal3 as $k3=>$v3){
            $personal3[$k3]['types']=1;
        }
        $person=array_merge($personal,$personal2,$personal3);
        //出租车位
        $where1['uuuid']=['eq',0];
        $shuju=DB::table('pk_carpos')->alias('c')
        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
        ->where(['c.uuid'=>$uid,'c.style'=>1,'c.types'=>2,'c.status'=>1])
        ->where($where1)
//        ->where('c.start_time','<=',$time)
        ->where('c.end_time','>=',$time)
        ->field('c.id,c.number,c.types,m.name,m.address,m.area,c.start_time,c.end_time,m.longitude,m.latitude')
        ->select();
//        var_dump($shuju);
        //共享车位
//        $shuju1=DB::table('pk_carpos')->alias('c')
//        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
//        ->where(['c.uuid'=>$uid,'c.style'=>1,'c.types'=>3,'c.charge1'=>null])
//        ->where($where1)
//            ->where('c.start_time','<=',$time)
//            ->where('c.end_time','>=',$time)
//        ->field('c.id,c.number,c.types,m.name,m.address,m.area,c.end_time')
//        ->select();
        $info=DB::table('pk_carpos')->alias('c')
        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
        ->where(['c.uuuid'=>$uid,'c.style'=>1,'c.types'=>3,'c.status'=>1])
//            ->where('c.start_time1','<=',$time)
            ->where('c.end_time1','>=',$time)
        ->field('c.id,c.number,c.types,m.name,m.address,m.area,c.start_time1,c.end_time1,m.longitude,m.latitude')
        ->select();

        foreach($info as $k=>$v){
            $info[$k]['start_time']=$v['start_time1'];
            $info[$k]['end_time']=$v['end_time1'];
            unset($info[$k]['end_time1']);
            unset($info[$k]['start_time1']);
        }
//        $data=array_merge($personal,$shuju,$shuju1,$info);
        $data=array_merge($person,$shuju,$info);
        if(!empty($data)){
            $maxpage=ceil(count($data)/$num);
            $data=my_sort($data,'types');
            $data=array_slice($data,$start,$num);
        }

        foreach($data as $k=>$v){
        	if($v['types']==1){
        		//表示个人车位
        		$data[$k]['end_time']='';
        	}else if($v['types']==2){
        		//表示出租车位
        		$data[$k]['end_time']=date('Y/m/d',$v['start_time']).'-'.date('Y/m/d',$v['end_time']);
        	}else if($v['types']==3){
        		//表示共享车位
        		$data[$k]['end_time']=date('Y/m/d H:i',$v['start_time']).'-'.date('Y/m/d H:i',$v['end_time']);
        	}
            $data[$k]['address']=$v['area'].$v['address'];
        }
        return json_encode(['code'=>1000,'data'=>$data,'maxpage'=>$maxpage,'msg'=>'车位信息获取成功']);
    }

    //历史车位
    public function historycarpos(){
    	$uid=input('uid/d');
        if (!TokenService::checkUserId($uid)) {
            return success(1002, '', '无效token');
        }
        //要把所有历史车位信息查询出来
        $page = input('page', 1);
        $num = 2;//每页显示的条数
        $start = ($page - 1) * $num;
        $data=DB::table('pk_historycarpos')->alias('c')
        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
        ->where(['c.uuid'=>$uid,'c.uuuid'=>['eq',0],'c.types'=>2])
        ->whereOr(['c.uuuid'=>$uid,'c.types'=>3])
        ->order('id desc')
        ->field('c.id,c.number,c.type,c.types,m.name,m.address,m.area,c.end_time,c.end_time1,m.longitude,m.latitude')
        ->limit($start, $num)
        ->select();
        $total=DB::table('pk_historycarpos')->alias('c')
        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
        ->where(['c.uuid'=>$uid,'c.uuuid'=>['eq',0],'c.types'=>2])
        ->whereOr(['c.uuuid'=>$uid,'c.types'=>3])->count();
        $maxpage=ceil($total/$num);
         foreach($data as $k=>$v){
            if(!empty($v['end_time1'])){
                $data[$k]['end_time']=$v['end_time1'];
                unset($data[$k]['end_time1']);
            }

            if($v['types']==2){
                //表示出租车位
                $data[$k]['end_time']=date('Y/m/d',$data[$k]['end_time']);
            }else if($v['types']==3){
                //表示共享车位
                $data[$k]['end_time']=date('Y/m/d H:i',$data[$k]['end_time']);
            }
            $data[$k]['address']=$v['area'].$v['address'];
        }
        return json_encode(['code'=>1000,'data'=>$data,'maxpage'=>$maxpage,'msg'=>'车位信息获取成功']);
    }

    //临时停车的历史车位
    Public function temstopcar(){
        $uid=input('uid/d');
        if (!TokenService::checkUserId($uid)) {
            return success(1002, '', '无效token');
        }
        $page = input('page', 1);
        $num = 2;//每页显示的条数
        $start = ($page - 1) * $num;
        $where['s.out_time']=['neq',0];
        $data=DB::table('pk_stopcarrecord')->alias('s')
        ->join('__MANAGER__ m','m.mg_id=s.mg_id')
        ->where($where)
        ->where(['s.uid'=>$uid])
        ->where('s.status',4)
        ->field('s.id,s.number,s.in_time,s.timelong,m.name,m.address,m.area,m.longitude,m.latitude')
        ->limit($start, $num)
        ->select();
        $total=DB::table('pk_stopcarrecord')->alias('s')
        ->join('__MANAGER__ m','m.mg_id=s.mg_id')
        ->where($where)
        ->where(['s.uid'=>$uid])
        ->where('s.status',4)
         ->count();
        $maxpage=ceil($total/$num);
        foreach($data as $k=>$v){
            $data[$k]['date']=date('Y/m/d',$v['in_time']);
            $data[$k]['timelong']=ceil($v['timelong']/3600);
            $data[$k]['address']=$v['area'].$v['address'];
        }   
        return json_encode(['code'=>1000,'data'=>$data,'maxpage'=>$maxpage,'msg'=>'临时停车历史车位获取成功']);
    }
    public function historycarposs(){
        $type = input('type',1); //1:出租共享，2:临时停车
        if($type==1){
            $uid=input('uid/d');
            if (!TokenService::checkUserId($uid)) {
                return success(1002, '', '无效token');
            }
            //要把所有历史车位信息查询出来
            $page = input('page', 1);
            $num = 2;//每页显示的条数
            $start = ($page - 1) * $num;
            $data=DB::table('pk_historycarpos')->alias('c')
            ->join('__MANAGER__ m','c.mg_id=m.mg_id')
            ->where(['c.uuid'=>$uid,'c.uuuid'=>['eq',0]])
            ->whereOr(['c.uuuid'=>$uid])
            ->field('c.id,c.number,c.types,m.name,m.address,m.area,c.end_time,c.end_time1')
            ->limit($start, $num)
            ->select();
            $total=DB::table('pk_historycarpos')->alias('c')
            ->join('__MANAGER__ m','c.mg_id=m.mg_id')
            ->where(['c.uuid'=>$uid,'c.uuuid'=>['eq',0]])
            ->whereOr(['c.uuuid'=>$uid])->count();
            $maxpage=ceil($total/$num);
             foreach($data as $k=>$v){
                if(!empty($v['end_time1'])){
                    $data[$k]['end_time']=$v['end_time1'];
                    unset($data[$k]['end_time1']);
                }
                if($v['types']==2){
                    //表示出租车位
                    $data[$k]['end_time']=date('Y/m/d',$data[$k]['end_time']);
                }else if($v['types']==3){
                    //表示共享车位
                    $data[$k]['end_time']=date('Y/m/d H:i',$data[$k]['end_time']);
                }
                $data[$k]['address']=$v['area'].$v['address'];
            }
        }else{
            $uid=input('uid/d');
            if (!TokenService::checkUserId($uid)) {
                return success(1002, '', '无效token');
            }
            $page = input('page', 1);
            $num = 2;//每页显示的条数
            $start = ($page - 1) * $num;
            $where['s.out_time']=['neq',0];
            $data=DB::table('pk_stopcarrecord')->alias('s')
            ->join('__MANAGER__ m','m.mg_id=s.mg_id')
            ->where($where)
            ->where(['uid'=>$uid])
            ->field('s.id,s.number,s.in_time,s.timelong,m.name,m.address,m.area')
            ->limit($start, $num)
            ->select();
            $total=DB::table('pk_stopcarrecord')->alias('s')
            ->join('__MANAGER__ m','m.mg_id=s.mg_id')
            ->where($where)
            ->where(['uid'=>$uid])->count();
            $maxpage=ceil($total/$num);
            foreach($data as $k=>$v){
                $data[$k]['date']=date('Y/m/d',$v['in_time']);
                $data[$k]['timelong']=ceil($v['timelong']/3600);
                $data[$k]['address']=$v['area'].$v['address'];
            }   
        }
        return json_encode(['code'=>1000,'data'=>$data,'maxpage'=>$maxpage,'msg'=>'历史车位获取成功']);
    }
    //车位详情
    public function detail(){
    	$id=input('id/d');
        $parkType=input('parkType/d');
        $ptypes=input('types/d');
//        1为车位详情  2为历史出租共享车位详情 3为历史临时车位详情
//        $type=input('type/d');
//        $number=input('number');
//        $mg_id=input('mg_id');
        if($parkType==1){

            if($ptypes==1){
                //此时表示个人车位
//                $types=DB::table('pk_carpos')->where(['id'=>$id])->field('types,owner,uuid')->find();
                $data=DB::table('pk_carpos')->alias('c')
                    ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                    ->join('__USER__ u','u.uid=c.uid')
                    ->where(['c.id'=>$id])
                    ->field('c.number,m.name,m.address,c.types,c.type,c.owner,m.longitude,m.latitude,m.area,c.logo,u.phone,m.mg_id')
                    ->find();
                $list['owner']='我';
                $list['number']=$data['number'];
                $list['name']=$data['name'];
                $list['address']=$data['area'].$data['address'];
                $list['type']='个人车位';
                $list['status']=$data['type'];
                $list['latitude']=$data['latitude'];
                $list['longitude']=$data['longitude'];
                $list['phone']=$data['phone'];
                $list['mg_id']=$data['mg_id'];
                $list['logo']=getImage($data['logo']);
            }else if($ptypes==2){
                $types=DB::table('pk_carpos')->where(['id'=>$id])->field('types,owner,uuid')->find();
                //此时表示是出租车位
                if($types['owner']==1){
                    //物业
                    $data=DB::table('pk_carpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
//                    ->join('__USER__ u','u.uid=c.uid')
                        ->where(['c.id'=>$id])
                        ->field('c.number,m.name,m.mg_id,m.address,m.longitude,m.latitude,m.phone,c.types,c.owner,c.end_time,c.charge,m.area,c.logo,m.longitude,m.latitude')
                        ->find();
                    $list['owner']='物业';
                }else if($types['owner']==2){
                    //个人
                    $data=DB::table('pk_carpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                        ->join('__USER__ u','u.uid=c.uid')
                        ->where(['c.id'=>$id])
                        ->field('c.number,m.name,m.mg_id,m.address,c.types,u.sex,u.username,u.phone,c.owner,c.end_time,c.charge,m.area,c.logo,m.longitude,m.latitude')
                        ->find();
                    if($data['sex']==2){
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."女士";
                    }else{
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."先生";
                    }

                }
                $list['number']=$data['number'];
                $list['name']=$data['name'];
                $list['address']=$data['area'].$data['address'];
                $list['type']='出租车位';
                $list['status']=5;
                $list['latitude']=$data['latitude'];
                $list['longitude']=$data['longitude'];
                $list['phone']=$data['phone'];
                $list['mg_id']=$data['mg_id'];
                $list['logo']=getImage($data['logo']);
                $list['end_time']=date('Y/m/d',$data['end_time']);
                $list['charge']=$data['charge'];
//            $list['address']=$data['area'].$data['address'];
            }else if($ptypes==3){
                //此时表示是共享车位
                $types=DB::table('pk_carpos')->where(['id'=>$id])->field('types,owner,uuid')->find();
                if($types['owner']==1){
                    //物业

                    $data=DB::table('pk_carpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                        ->join('__USER__ u','u.uid=c.uid')
                        ->where(['c.id'=>$id])
                        ->field('c.number,c.mg_id,m.name,m.phone,m.address,m.area,c.types,c.logo,c.owner,c.end_time1,c.charge1,c.date,c.end_time1,m.longitude,m.latitude')
                        ->find();
                    if($data['sex']==2){
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."女士";
                    }else{
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."先生";
                    }
                }else{
                    if($types['uuid']==0){
                       $str="u.uid=c.uid";
                    }else{
                        $str="u.uid=c.uuid";
                    }
                    $data=DB::table('pk_carpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                        ->join('__USER__ u',$str)
                        ->where(['c.id'=>$id])
                        ->field('c.number,c.mg_id,m.name,m.address,m.area,c.types,c.uid,c.logo,c.owner,c.end_time,c.charge1,c.date,u.username,u.phone,u.sex,c.end_time1,m.longitude,m.latitude')
                        ->find();
                    if($data['sex']==2){
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."女士";
                    }else{
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."先生";
                    }
                }
                $list['number']=$data['number'];
                $list['name']=$data['name'];
                $list['address']=$data['area'].$data['address'];
                $list['type']='共享车位';
                $list['status']=6;
                $list['latitude']=$data['latitude'];
                $list['longitude']=$data['longitude'];
                $list['phone']=$data['phone'];
                $list['mg_id']=$data['mg_id'];
                $list['logo']=getImage($data['logo']);
                $list['address']=$data['area'].$data['address'];
//	    	if(!empty($data['end_time1'])){
                $list['end_time']=date('Y/m/d H:i',$data['end_time1']);
                $list['charge']=$data['charge1'];
//	    	}else{
//	    		$data['end_time']=date('Y/m/d H:i',$data['end_time']);
//	    	}
            }
        }else if($parkType==2){
            $types=DB::table('pk_historycarpos')->where(['id'=>$id])->field('types,owner,uuid')->find();
            if($types['types']==2){
                if($types['owner']==1){
                    //物业
                    $data=DB::table('pk_historycarpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                        //                    ->join('__USER__ u','u.uid=c.uid')
                        ->where(['c.id'=>$id])
                        ->field('c.number,m.name,m.mg_id,m.address,m.phone,c.types,c.owner,c.end_time,c.charge,m.area,c.logo,m.longitude,m.latitude')
                        ->find();
                    $list['owner']='物业';
                }else if($types['owner']==2){
                    //个人
                    $data=DB::table('pk_historycarpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                        ->join('__USER__ u','u.uid=c.uid')
                        ->where(['c.id'=>$id])
                        ->field('c.number,m.name,m.mg_id,m.address,c.types,u.sex,u.username,u.phone,c.owner,c.end_time,c.charge,m.area,c.logo,m.longitude,m.latitude')
                        ->find();
                    if($data['sex']==2){
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."女士";
                    }else{
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."先生";
                    }

                }
                $list['number']=$data['number'];
                $list['name']=$data['name'];
                $list['address']=$data['area'].$data['address'];
                $list['type']='出租车位';
                $list['status']='已过期';
                $list['latitude']=$data['latitude'];
                $list['longitude']=$data['longitude'];
                $list['phone']=$data['phone'];
                $list['mg_id']=$data['mg_id'];
                $list['logo']=getImage($data['logo']);
                $list['end_time']=date('Y/m/d',$data['end_time']);
                $list['charge']=$data['charge'];
            }else if($types['types']==3){
                //此时表示是共享车位
                if($types['owner']==1){
                    //物业
                    $data=DB::table('pk_historycarpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                        ->where(['c.id'=>$id])
                        ->field('c.number,c.mg_id,m.name,m.phone,m.address,m.area,c.types,c.logo,c.owner,c.end_time1,c.charge1,c.date,c.end_time1,m.longitude,m.latitude')
                        ->find();
                    $list['owner']='物业';
                }else{
                    if($types['uuid']==0){
                        $str="u.uid=c.uid";
                    }else{
                        $str="u.uid=c.uuid";
                    }
                    $data=DB::table('pk_historycarpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                        ->join('__USER__ u',$str)
                        ->where(['c.id'=>$id])
                        ->field('c.number,c.mg_id,m.name,m.address,m.area,c.types,c.uid,c.logo,c.owner,c.end_time,c.charge1,c.date,u.username,u.phone,u.sex,c.end_time1,m.longitude,m.latitude')
                        ->find();
                    if($data['sex']==2){
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."女士";
                    }else{
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."先生";
                    }
                }
                $list['number']=$data['number'];
                $list['name']=$data['name'];
                $list['address']=$data['area'].$data['address'];
                $list['type']='共享车位';
                $list['status']='已过期';
                $list['latitude']=$data['latitude'];
                $list['longitude']=$data['longitude'];
                $list['phone']=$data['phone'];
                $list['mg_id']=$data['mg_id'];
                $list['logo']=getImage($data['logo']);
                $list['address']=$data['area'].$data['address'];
//	    	if(!empty($data['end_time1'])){
                $list['end_time']=date('Y/m/d H:i',$data['end_time1']);
                $list['charge']=$data['charge1'];
            }
        }else{
            $flag=Db::table('pk_stopcarrecord')->where(['id'=>$id])->field('mg_id,number,duration,in_time,timelong,out_time')->find();
            $idd=DB::table('pk_historycarpos')->where(['mg_id'=>$flag['mg_id'],'number'=>$flag['number'],'types'=>4,'end_time'=>$flag['out_time']])->field('id,owner')->find();
            if($idd){
                if($idd['owner']==1){
                    //物业
                    $data=DB::table('pk_historycarpos')->alias('c')
                        ->join('__MANAGER__ m','m.mg_id=c.mg_id')
                        ->where(['c.id'=>$idd['id']])
                        ->field('c.number,c.mg_id,m.name,m.address,m.area,c.charge,m.longitude,m.latitude,m.phone,m.logo')
                        ->find();
                    $list['owner']='物业';
                }else{
                    $data=DB::table('pk_historycarpos')->alias('c')
                        ->join('__MANAGER__ m','m.mg_id=c.mg_id')
                        ->join("__USER__ u",'c.uid=u.uid')
                        ->where(['c.id'=>$idd['id']])
                        ->field('c.number,c.mg_id,m.name,m.address,m.area,c.charge,u.username,u.phone,m.longitude,m.latitude,u.sex,u.phone,m.logo')
                        ->find();
                    if($data['sex']==2){
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."女士";
                    }else{
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."先生";
                    }
                }
//        $data['date']=date('Y/m/d',$flag['in_time']);
                $list['number']=$data['number'];
                $list['name']=$data['name'];
                $list['address']=$data['area'].$data['address'];
                $list['type']='临时车位';
                $list['status']='已过期';
                $list['latitude']=$data['latitude'];
                $list['longitude']=$data['longitude'];
                $list['phone']=$data['phone'];
                $list['mg_id']=$data['mg_id'];
                $list['logo']=getImage($data['logo']);
                $list['address']=$data['area'].$data['address'];
//	    	if(!empty($data['end_time1'])){
                $list['duration']=date('Y/m/d',$flag['duration']);
                $list['charge']=$data['charge'];
                $list['timelong']=ceil($flag['timelong']/3600);
            }
        }

//        $data['logo']=substr($data['logo'],1);
    	if(!empty($list)){
    		return json_encode(['code'=>1000,'data'=>$list,'msg'=>'车位详情获取成功']);
    	}else{
    		return json_encode(['code'=>1001,'msg'=>'车位详情获取失败']);
    	}
    }

    public function historydetails(){
        $type =input('type'); //1:表示出租共享，2:表示临时停车
        if($type==1){
            $id=input('id');
            $types=DB::table('pk_historycarpos')->where(['id'=>$id])->field('types,owner')->find();
            if($types['types']==1){
                //此时表示个人车位
                $data=DB::table('pk_historycarpos')->alias('c')
                    ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                    ->where(['c.id'=>$id])
                    ->field('c.number,m.name,m.address,c.types,c.owner,m.area,c.logo')
                    ->find();
                $data['address']=$data['area'].$data['address'];
            }else if($types['types']==2){
                //此时表示是出租车位
                if($types['owner']==1){
                    $data=DB::table('pk_historycarpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                        ->where(['c.id'=>$id])
                        ->field('c.number,m.name,m.address,c.types,c.owner,c.end_time,c.charge,m.area,c.logo')
                        ->find();
                }else if($types['owner']==2){
                    $data=DB::table('pk_historycarpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                        ->join('__USER__ u','u.uid=c.uid')
                        ->where(['c.id'=>$id])
                        ->field('c.number,m.name,m.address,c.types,u.username,u.phone,c.owner,c.end_time,c.charge,m.area,c.logo')
                        ->find();
                }
                $data['end_time']=date('Y/m/d',$data['end_time']);
                $data['address']=$data['area'].$data['address'];
            }else if($types['types']==3){
                //此时表示是共享车位
                if($types['owner']==1){
                    $data=DB::table('pk_historycarpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                        ->where(['c.id'=>$id])
                        ->field('c.number,m.name,m.address,m.area,c.types,c.uid,c.logo,c.owner,c.end_time,c.charge,c.date,c.end_time1')
                        ->find();
                }else if($types['owner']==2){
                    $data=DB::table('pk_historycarpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                        ->join('__USER__ u','u.uid=c.uid')
                        ->where(['c.id'=>$id])
                        ->field('c.number,m.name,m.address,m.area,c.types,c.uid,c.logo,c.owner,c.end_time,c.charge,c.date,u.username,u.phone,c.end_time1')
                        ->find();
                }
                $data['address']=$data['area'].$data['address'];
                if(!empty($data['end_time1'])){
                    $data['end_time']=date('Y/m/d H:i',$data['end_time1']);
                }else{
                    $data['end_time']=date('Y/m/d H:i',$data['end_time']);
                }
            }
            $data['logo']=substr($data['logo'],1);
        }else{
            $id=input('id');//临时停车表的id
            $flag=Db::table('pk_stopcarrecord')->where(['id'=>$id])->field('mg_id,number,in_time,timelong')->find();
            $idd=DB::table('pk_carpos')->where(['mg_id'=>$flag['mg_id'],'number'=>$flag['number']])->field('id,owner')->find();
            if($idd['owner']==1){
                $data=DB::table('pk_carpos')->alias('c')
                ->join('__MANAGER__ m','m.mg_id=c.mg_id')
                ->where(['c.id'=>$idd['id']])
                ->field('c.number,m.name,m.address,m.area,c.charge')
                ->find();
            }else{
                $data=DB::table('pk_carpos')->alias('c')
                ->join('__MANAGER__ m','m.mg_id=c.mg_id')
                ->join("__USER__ u",'c.uid=u.uid')
                ->where(['c.id'=>$idd['id']])
                ->field('c.number,m.name,m.address,m.area,c.charge1,u.username,u.phone')
                ->find();
            }
            dump($flag);die;
            $data['date']=date('Y/m/d',$flag['in_time']);
            $data['timelong']=ceil($flag['timelong']/3600);
            $data['address']=$data['area'].$data['address'];
        }
        return json_encode(['code'=>1000,'data'=>$data,'msg'=>'车位详情获取成功']);
    }

    //历史车位详情
    public function historydetail(){
        $id=input('id/d');
        $types=DB::table('pk_historycarpos')->where(['id'=>$id])->field('types,owner')->find();
//        if($types['types']==1){
//            //此时表示个人车位
//            $data=DB::table('pk_historycarpos')->alias('c')
//                ->join('__MANAGER__ m','c.mg_id=m.mg_id')
//                ->where(['c.id'=>$id])
//                ->field('c.number,m.name,m.address,c.types,c.owner,m.area,c.logo')
//                ->find();
//            $data['address']=$data['area'].$data['address'];
//        }else
        if($types['types']==2){
                if($types['owner']==1){
                    //物业
                    $data=DB::table('pk_historycarpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
        //                    ->join('__USER__ u','u.uid=c.uid')
                        ->where(['c.id'=>$id])
                        ->field('c.number,m.name,m.mg_id,m.address,m.phone,c.types,c.owner,c.end_time,c.charge,m.area,c.logo,m.longitude,m.latitude')
                        ->find();
                    $list['owner']='物业';
                }else if($types['owner']==2){
                    //个人
                    $data=DB::table('pk_historycarpos')->alias('c')
                        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                        ->join('__USER__ u','u.uid=c.uid')
                        ->where(['c.id'=>$id])
                        ->field('c.number,m.name,m.mg_id,m.address,c.types,u.sex,u.username,u.phone,c.owner,c.end_time,c.charge,m.area,c.logo,m.longitude,m.latitude')
                        ->find();
                    if($data['sex']==2){
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."女士";
                    }else{
                        $list['owner']=mb_substr($data['username'],0,1,'utf-8')."先生";
                    }

                }
            $list['number']=$data['number'];
            $list['name']=$data['name'];
            $list['address']=$data['area'].$data['address'];
            $list['type']='出租车位';
            $list['status']='已过期';
            $list['latitude']=$data['latitude'];
            $list['longitude']=$data['longitude'];
            $list['phone']=$data['phone'];
            $list['mg_id']=$data['mg_id'];
            $list['logo']=getImage($data['logo']);
            $list['end_time']=date('Y/m/d',$data['end_time']);
            $list['charge']=$data['charge'];
//            if($types['owner']==1){
//                $data=DB::table('pk_historycarpos')->alias('c')
//                    ->join('__MANAGER__ m','c.mg_id=m.mg_id')
//                    ->where(['c.id'=>$id])
//                    ->field('c.number,m.name,m.address,c.types,c.owner,c.end_time,c.charge,m.area,c.logo')
//                    ->find();
//            }else if($types['owner']==2){
//                $data=DB::table('pk_historycarpos')->alias('c')
//                    ->join('__MANAGER__ m','c.mg_id=m.mg_id')
//                    ->join('__USER__ u','u.uid=c.uid')
//                    ->where(['c.id'=>$id])
//                    ->field('c.number,m.name,m.address,c.types,u.username,u.phone,c.owner,c.end_time,c.charge,m.area,c.logo')
//                    ->find();
//            }
//            $data['end_time']=date('Y/m/d',$data['end_time']);
//            $data['address']=$data['area'].$data['address'];
        }else if($types['types']==3){
            //此时表示是共享车位
            if($types['owner']==1){
                //物业
                $data=DB::table('pk_historycarpos')->alias('c')
                    ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                    ->where(['c.id'=>$id])
                    ->field('c.number,c.mg_id,m.name,m.phone,m.address,m.area,c.types,c.logo,c.owner,c.end_time1,c.charge1,c.date,c.end_time1,m.longitude,m.latitude')
                    ->find();
                $list['owner']='物业';
            }else{
                $data=DB::table('pk_historycarpos')->alias('c')
                    ->join('__MANAGER__ m','c.mg_id=m.mg_id')
                    ->join('__USER__ u','u.uid=c.uid')
                    ->where(['c.id'=>$id])
                    ->field('c.number,c.mg_id,m.name,m.address,m.area,c.types,c.uid,c.logo,c.owner,c.end_time,c.charge1,c.date,u.username,u.phone,u.sex,c.end_time1,m.longitude,m.latitude')
                    ->find();
                if($data['sex']==2){
                    $list['owner']=mb_substr($data['username'],0,1,'utf-8')."女士";
                }else{
                    $list['owner']=mb_substr($data['username'],0,1,'utf-8')."先生";
                }
            }
            $list['number']=$data['number'];
            $list['name']=$data['name'];
            $list['address']=$data['area'].$data['address'];
            $list['type']='共享车位';
            $list['status']='已过期';
            $list['latitude']=$data['latitude'];
            $list['longitude']=$data['longitude'];
            $list['phone']=$data['phone'];
            $list['mg_id']=$data['mg_id'];
            $list['logo']=getImage($data['logo']);
            $list['address']=$data['area'].$data['address'];
//	    	if(!empty($data['end_time1'])){
            $list['end_time']=date('Y/m/d H:i',$data['end_time1']);
            $list['charge']=$data['charge1'];
        }
//        $data['logo']=substr($data['logo'],1);
    	if(!empty($list)){
    		return json_encode(['code'=>1000,'data'=>$list,'msg'=>'车位详情获取成功']);
    	}else{
    		return json_encode(['code'=>1001,'msg'=>'车位详情获取失败']);
    	}
    }

    //临时停车
    public function temstopcardetail(){
        $id=input('id/d');//临时停车表的id
        $flag=Db::table('pk_stopcarrecord')->where(['id'=>$id])->field('mg_id,number,duration,in_time,timelong,out_time')->find();
        $idd=DB::table('pk_historycarpos')->where(['mg_id'=>$flag['mg_id'],'number'=>$flag['number'],'types'=>4,'update_time'=>$flag['out_time']])->field('id,owner')->find();
        if($idd){
            if($idd['owner']==1){
                //物业
                $data=DB::table('pk_historycarpos')->alias('c')
                    ->join('__MANAGER__ m','m.mg_id=c.mg_id')
                    ->where(['c.id'=>$idd['id']])
                    ->field('c.number,c.mg_id,m.name,m.address,m.area,c.charge,m.longitude,m.latitude,m.phone,m.logo')
                    ->find();
                $list['owner']='物业';
            }else{

                $data=DB::table('pk_historycarpos')->alias('c')
                    ->join('__MANAGER__ m','m.mg_id=c.mg_id')
                    ->join("__USER__ u",'c.uid=u.uid')
                    ->where(['c.id'=>$idd['id']])
                    ->field('c.number,c.mg_id,m.name,m.address,m.area,c.charge,u.username,u.phone,m.longitude,m.latitude,u.sex,u.phone,m.logo')
                    ->find();
                if($data['sex']==2){
                    $list['owner']=mb_substr($data['username'],0,1,'utf-8')."女士";
                }else{
                    $list['owner']=mb_substr($data['username'],0,1,'utf-8')."先生";
                }
            }
//        $data['date']=date('Y/m/d',$flag['in_time']);
            $list['number']=$data['number'];
            $list['name']=$data['name'];
            $list['address']=$data['area'].$data['address'];
            $list['type']='临时车位';
            $list['status']='已过期';
            $list['latitude']=$data['latitude'];
            $list['longitude']=$data['longitude'];
            $list['phone']=$data['phone'];
            $list['mg_id']=$data['mg_id'];
            $list['logo']=getImage($data['logo']);
            $list['address']=$data['area'].$data['address'];
//	    	if(!empty($data['end_time1'])){
            $list['duration']=date('Y/m/d',$flag['duration']);
            $list['charge']=$data['charge'];
            $list['timelong']=ceil($flag['timelong']/3600);
        }

//        $data['address']=$data['area'].$data['address'];
        if(!empty($list)){
            return json_encode(['code'=>1000,'data'=>$list,'msg'=>'临时停车详情获取成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'临时停车详情获取失败']);
        }
    }
    public function test(){
        $time='2018-11-20 21:13:00';
//        strtotime($time);
        return json_encode(['code'=>1000,'data'=>strtotime($time),'msg'=>'临时停车详情获取成功']);
    }
}
