<?php

namespace app\api\controller;

use app\api\service\TokenService;
use think\Controller;
use think\Request;
use think\Db;
use app\api\Model\Carpos;
use app\api\Model\MAnager;

class MessageController extends Controller
{
    //这个车位是否已发
    /*首先判断这个车位是出租共享或临时
    1.出租共享：
                判断用户现在使用的车位以及过期车位，判断这些车位是否发送出租共享信息以及到期信息
    如果未发送消息
                判断车位类型出租共享临时，再判断时间加入消息
    如果发送消息，判断发送的什么类型的消息，还有什么消息未发送

    */
    public function listIndex(){
        $uid=input('uid/d');
        if (!TokenService::checkUserId($uid)) {
            return success(1002, '', '无效token');
        }
//        $time=time();
        $page = input('page/d', 1);
        $num =10;//每页显示的条数
        $maxpage=1;
        $start = ($page - 1) * $num;
        $where2['uuid']=array('eq',0);
        $time=time();
        //出租车位
        $where1['uuuid']=['eq',0];
        $shuju=DB::table('pk_carpos')->alias('c')
            ->join('pk_manager m','c.mg_id=m.mg_id')
            ->where(['c.uuid'=>$uid,'c.style'=>1,'c.types'=>2])
            ->where($where1)
            ->where('c.end_time','<=',$time)
            ->whereOr('c.end_time','>=',$time)
            ->field('c.id,c.number,c.types,c.start_time,c.end_time,c.mg_id')
            ->select();

        //共享车位
//        $shuju1=DB::table('pk_carpos')->alias('c')
//            ->join('__MANAGER__ m','c.mg_id=m.mg_id')
//            ->where(['c.uuid'=>$uid,'c.style'=>1,'c.types'=>3,'c.charge1'=>null])
//            ->where($where1)
//            ->where('c.end_time','<=',$time)
//            ->whereOr('c.end_time','>=',$time)
//            ->field('c.id,c.number,c.types,c.start_time,c.end_time,c.mg_id')
//            ->select();
        $info=DB::table('pk_carpos')->alias('c')
            ->join('__MANAGER__ m','c.mg_id=m.mg_id')
            ->where(['c.uuuid'=>$uid,'c.style'=>1])
            ->where('c.end_time1','<=',$time)
            ->whereOr('c.end_time1','>=',$time)
            ->field('c.id,c.number,c.types,c.start_time1,c.end_time1,c.mg_id')
            ->select();
        foreach($info as $k=>$v){
            $info[$k]['start_time']=$v['start_time1'];
            $info[$k]['end_time']=$v['end_time1'];
            unset($info[$k]['end_time1']);
            unset($info[$k]['start_time1']);
        }
        $res=array_merge($shuju,$info);
        foreach ($res as $key){
            $list['uid']=$uid;
            $list['mg_id']=$key['mg_id'];
            $list['p_number']=$key['number'];
            $list['status']=$key['types'];
            $list1['starttime']=$key['start_time'];
            $list['endtime']=$key['end_time'];
            $manager=Db::table('pk_manager')->where('mg_id',$list['mg_id'])->find('mg_name');
            $con=Db::table('pk_message')->where($list)->order('createtime asc')->select();//查询用户是否已接收过消息
            $con1=Db::table('pk_message')->where($list)->where('type',1)->select();//查询用户是否已接收过月提醒消息
            $con2=Db::table('pk_message')->where($list)->where('type',2)->select();//查询用户是否已接收过周提醒消息
            $con3=Db::table('pk_message')->where($list)->where('type',3)->select();//查询用户是否已接收过续费消息
            $con4=Db::table('pk_message')->where($list)->where('type',4)->select();//查询用户是否已接收过消息
            $con5=Db::table('pk_message')->where($list)->where('type',5)->select();
            if(!empty($con)){
                     $aa['endtime']=date('Y-m-d H:i',$list['endtime']);
                     if($list['status']==2){
                         //出租车位
                         $yestoday=$list['endtime']+30*24*60*60;//30天前的时间戳
                         $week=$list['endtime']+7*24*60*60;//一周前
                         $aa['yestoday']=date('Y-m-d',$yestoday);
                         $aa['week']=date('Y-m-d',$week);
                         $aa['endtime']=date('Y-m-d H:i',$list['endtime']);
                         if($list['endtime']<$time){
                             if(!isset($con1) && empty($con1)){
                                 $adds2=array(
                                     'uid'=>$uid,
                                     'mg_id'=>$list['mg_id'],
                                     'p_number'=>$list['p_number'],
                                     // 'end_time'=> $list['endtime'],
                                     'title'=>'车位到期提醒',
                                     'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                                     'createtime'=>$yestoday,
                                     'updatetime'=>$yestoday,
                                     'endtime'=>$list['endtime'],
                                     'type'=>1,
                                     'status'=>$list['status']
                                 );
                                 Db::table('pk_message')->insert($adds2);
                             }
                             if (!isset($con2) && empty($con2)){
                                 $adds3=array(
                                     'uid'=>$uid,
                                     'mg_id'=>$list['mg_id'],
                                     'p_number'=>$list['p_number'],
                                     // 'end_time'=> $list['endtime'],
                                     'title'=>'车位到期提醒',
                                     'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                                     'createtime'=>$week,
                                     'updatetime'=>$week,
                                     'endtime'=>$list['endtime'],
                                     'type'=>2,
                                     'status'=>$list['status']
                                 );
                                 Db::table('pk_message')->insert($adds3);
                             }
                             if (!isset($con3) && empty($con3)){
                                 $adds4=array(
                                     'uid'=>$uid,
                                     'mg_id'=>$list['mg_id'],
                                     'p_number'=>$list['p_number'],
                                     // 'end_time'=> $list['endtime'],
                                     'title'=>'车位到期续费提醒',
                                     'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期，请及时续费。",
                                     'createtime'=>$list['endtime'],
                                     'updatetime'=>$list['endtime'],
                                     'endtime'=>$list['endtime'],
                                     'type'=>3,
                                     'status'=>$list['status']
                                 );
                                 Db::table('pk_message')->insert($adds4);
                             }
                             if (!isset($con5) && empty($con5)){
                                 $adds5=array(
                                     'uid'=>$uid,
                                     'mg_id'=>$list['mg_id'],
                                     'p_number'=>$list['p_number'],
                                     // 'end_time'=> $list['endtime'],
                                     'title'=>'车位过期提醒',
                                     'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位已过期",
                                     'createtime'=>$time,
                                     'updatetime'=>$time,
                                     'endtime'=>$list['endtime'],
                                     'type'=>5,
                                     'status'=>$list['status']
                                 );
                                 Db::table('pk_message')->insert($adds5);
                             }
                         }
                         if($time<$week){
                             if(!isset($con1) && empty($con1)){
                                 $adds2=array(
                                     'uid'=>$uid,
                                     'mg_id'=>$list['mg_id'],
                                     'p_number'=>$list['p_number'],
                                     // 'end_time'=> $list['endtime'],
                                     'title'=>'车位到期提醒',
                                     'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                                     'createtime'=>$yestoday,
                                     'updatetime'=>$yestoday,
                                     'endtime'=>$list['endtime'],
                                     'type'=>1,
                                     'status'=>$list['status']
                                 );
                                 Db::table('pk_message')->insert($adds2);
                             }
                             if (!isset($con2) && empty($con2)){
                                 $adds3=array(
                                     'uid'=>$uid,
                                     'mg_id'=>$list['mg_id'],
                                     'p_number'=>$list['p_number'],
                                     // 'end_time'=> $list['endtime'],
                                     'title'=>'车位到期提醒',
                                     'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                                     'createtime'=>$week,
                                     'updatetime'=>$week,
                                     'endtime'=>$list['endtime'],
                                     'type'=>2,
                                     'status'=>$list['status']
                                 );
                                 Db::table('pk_message')->insert($adds3);
                             }
                         }
                         if($time>$week && $time<=$list['endtime']){
                             if(!isset($con1) && empty($con1)){
                                 $adds2=array(
                                     'uid'=>$uid,
                                     'mg_id'=>$list['mg_id'],
                                     'p_number'=>$list['p_number'],
                                     // 'end_time'=> $list['endtime'],
                                     'title'=>'车位到期提醒',
                                     'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                                     'createtime'=>$yestoday,
                                     'updatetime'=>$yestoday,
                                     'endtime'=>$list['endtime'],
                                     'type'=>1,
                                     'status'=>$list['status']
                                 );
                                 Db::table('pk_message')->insert($adds2);
                             }
                             if (!isset($con2) && empty($con2)){
                                 $adds3=array(
                                     'uid'=>$uid,
                                     'mg_id'=>$list['mg_id'],
                                     'p_number'=>$list['p_number'],
                                     // 'end_time'=> $list['endtime'],
                                     'title'=>'车位到期提醒',
                                     'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                                     'createtime'=>$week,
                                     'updatetime'=>$week,
                                     'endtime'=>$list['endtime'],
                                     'type'=>2,
                                     'status'=>$list['status']
                                 );
                                 Db::table('pk_message')->insert($adds3);
                             }
                             if (!isset($con3) && empty($con3)){
                                 $adds4=array(
                                     'uid'=>$uid,
                                     'mg_id'=>$list['mg_id'],
                                     'p_number'=>$list['p_number'],
                                     // 'end_time'=> $list['endtime'],
                                     'title'=>'车位到期续费提醒',
                                     'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期，请及时续费。",
                                     'createtime'=>$list['endtime'],
                                     'updatetime'=>$list['endtime'],
                                     'endtime'=>$list['endtime'],
                                     'type'=>3,
                                     'status'=>$list['status']
                                 );
                                 Db::table('pk_message')->insert($adds4);
                             }
                         }

                     }
                     if($list['status']==3){
                         //共享车位
                         $minutes=$list['endtime']+15*60;//15分钟前的时间戳
//                         $aa['endtime']=date('Y-m-d H:i',$list['endtime']);

//                         $con5=Db::table('pk_message')->where($list)->order('createtime asc')->select();//查询用户是否已接收过消息
                         if($time<$minutes){
                             if(!isset($con4) && empty($con4)){
                                 $ads2=array(
                                     'uid'=>$uid,
                                     'mg_id'=>$list['mg_id'],
                                     'p_number'=>$list['p_number'],
                                     // 'end_time'=> $list['endtime'],
                                     'title'=>'车位到期提醒',
                                     'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                                     'createtime'=>$time,
                                     'updatetime'=>$time,
                                     'endtime'=>$list['endtime'],
                                     'type'=>4,
                                     'status'=>$list['status']
                                 );
                                 Db::table('pk_message')->insert($ads2);
                             }
                         }
                         if ($list['endtime']>$time && $minutes<$time){
                             if(!isset($con4) && empty($con4)){
                                 $ads2=array(
                                     'uid'=>$uid,
                                     'mg_id'=>$list['mg_id'],
                                     'p_number'=>$list['p_number'],
                                     // 'end_time'=> $list['endtime'],
                                     'title'=>'车位到期提醒',
                                     'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                                     'createtime'=>$minutes,
                                     'updatetime'=>$minutes,
                                     'endtime'=>$list['endtime'],
                                     'type'=>4,
                                     'status'=>$list['status']
                                 );
                                 Db::table('pk_message')->insert($ads2);
                             }
                         }
                         if ($list['endtime']<$time){
                             if(!isset($con4) && empty($con4)){
                                 $ads2=array(
                                     'uid'=>$uid,
                                     'mg_id'=>$list['mg_id'],
                                     'p_number'=>$list['p_number'],
                                     // 'end_time'=> $list['endtime'],
                                     'title'=>'车位到期提醒',
                                     'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                                     'createtime'=>$minutes,
                                     'updatetime'=>$minutes,
                                     'endtime'=>$list['endtime'],
                                     'type'=>4,
                                     'status'=>$list['status']
                                 );
                                 Db::table('pk_message')->insert($ads2);
                             }
                             if(!isset($con5) && empty($con5)){
                                 $adms3=array(
                                     'uid'=>$uid,
                                     'mg_id'=>$list['mg_id'],
                                     'p_number'=>$list['p_number'],
                                     // 'end_time'=> $list['endtime'],
                                     'title'=>'车位过期提醒',
                                     'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位已过期。",
                                     'createtime'=>$time,
                                     'updatetime'=>$time,
                                     'endtime'=>$list['endtime'],
                                     'type'=>5,
                                     'status'=>$list['status']
                                 );
                                 Db::table('pk_message')->insert($adms3);
                             }
                         }



                     }

            }else{
                if($list['status']==2){
                    //出租车位
                    $yestoday=$list['endtime']+30*24*60*60;//30天前的时间戳
                    $week=$list['endtime']+7*24*60*60;//一周前
                    $aa['yestoday']=date('Y-m-d',$yestoday);
                    $aa['week']=date('Y-m-d',$week);
                    $aa['endtime']=date('Y-m-d H:i',$list['endtime']);
                    if($list['endtime']<$time){
                        $adds=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'系统消息',
                            'message'=>"您租用的{$manager['mg_name']}{$list['p_number']}车位租用成功",
                            'createtime'=>$list1['starttime'],
                            'updatetime'=>$list1['starttime'],
                            'endtime'=>$list['endtime'],
                            'type'=>6,
                            'status'=>$list['status']
                        );
                        $adds2=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位到期提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                            'createtime'=>$yestoday,
                            'updatetime'=>$yestoday,
                            'endtime'=>$list['endtime'],
                            'type'=>1,
                            'status'=>$list['status']
                        );
                        $adds3=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位到期提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                            'createtime'=>$week,
                            'updatetime'=>$week,
                            'endtime'=>$list['endtime'],
                            'type'=>2,
                            'status'=>$list['status']
                        );
                        $adds4=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位到期续费提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期，请及时续费。",
                            'createtime'=>$list['endtime'],
                            'updatetime'=>$list['endtime'],
                            'endtime'=>$list['endtime'],
                            'type'=>3,
                            'status'=>$list['status']
                        );
                        $adds5=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位过期提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位已过期。",
                            'createtime'=>$time,
                            'updatetime'=>$time,
                            'endtime'=>$list['endtime'],
                            'type'=>5,
                            'status'=>$list['status']
                        );
                        $saves=Db::table('pk_message')->insert($adds);
                        $saves2=Db::table('pk_message')->insert($adds2);
                        $saves3=Db::table('pk_message')->insert($adds3);
                        $saves4=Db::table('pk_message')->insert($adds4);
                        $saves5=Db::table('pk_message')->insert($adds5);
                    }else if($time<$yestoday){
                        $add=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'系统消息',
                            'message'=>"您租用的{$manager['mg_name']}{$list['p_number']}车位租用成功",
                            'createtime'=>$list1['starttime'],
                            'updatetime'=>$list1['starttime'],
                            'endtime'=>$list['endtime'],
                            'type'=>6,
                            'status'=>$list['status']
                        );
                        $add2=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位到期提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                            'createtime'=>$time,
                            'updatetime'=>$time,
                            'endtime'=>$list['endtime'],
                            'type'=>1,
                            'status'=>$list['status']
                        );
                        $save=Db::table('pk_message')->insert($add);
                        $save2=Db::table('pk_message')->insert($add2);
                    }else if($time<$week){
                        $ad=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'系统消息',
                            'message'=>"您租用的{$manager['mg_name']}{$list['p_number']}车位租用成功",
                            'createtime'=>$list1['starttime'],
                            'updatetime'=>$list1['starttime'],
                            'endtime'=>$list['endtime'],
                            'type'=>6,
                            'status'=>$list['status']
                        );
                        $ad2=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位到期提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                            'createtime'=>$yestoday,
                            'updatetime'=>$yestoday,
                            'endtime'=>$list['endtime'],
                            'type'=>1,
                            'status'=>$list['status']
                        );
                        $ad3=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位到期提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                            'createtime'=>$time,
                            'updatetime'=>$time,
                            'endtime'=>$list['endtime'],
                            'type'=>2,
                            'status'=>$list['status']
                        );
                        $saves=Db::table('pk_message')->insert($ad);
                        $saves2=Db::table('pk_message')->insert($ad2);
                        $saves3=Db::table('pk_message')->insert($ad3);
                    }else if($time>$week && $time<$list['endtime']){
                        $up=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'系统消息',
                            'message'=>"您租用的{$manager['mg_name']}{$list['p_number']}车位租用成功",
                            'createtime'=>$list1['starttime'],
                            'updatetime'=>$list1['starttime'],
                            'endtime'=>$list['endtime'],
                            'type'=>6,
                            'status'=>$list['status']
                        );
                        $up2=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位到期提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                            'createtime'=>$yestoday,
                            'updatetime'=>$yestoday,
                            'endtime'=>$list['endtime'],
                            'type'=>1,
                            'status'=>$list['status']
                        );
                        $up3=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位到期提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                            'createtime'=>$week,
                            'updatetime'=>$week,
                            'endtime'=>$list['endtime'],
                            'type'=>2,
                            'status'=>$list['status']
                        );
                        $up4=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位到期续费提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期，请及时续费。",
                            'createtime'=>$list['endtime'],
                            'updatetime'=>$list['endtime'],
                            'endtime'=>$list['endtime'],
                            'type'=>3,
                            'status'=>$list['status']
                        );
                        $saves=Db::table('pk_message')->insert($up);
                        $saves2=Db::table('pk_message')->insert($up2);
                        $saves3=Db::table('pk_message')->insert($up3);
                        $saves4=Db::table('pk_message')->insert($up4);
                    }
                }else{
                    //共享车位
                    $minutes=$list['endtime']+15*60;//15分钟前的时间戳
                    $aa['endtime']=date('Y-m-d H:i',$list['endtime']);
                    if($list['endtime']>$time && $time>$minutes){
                        $ads=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'系统消息',
                            'message'=>"您租用的{$manager['mg_name']}{$list['p_number']}车位租用成功",
                            'createtime'=>$list1['starttime'],
                            'updatetime'=>$list1['starttime'],
                            'endtime'=>$list['endtime'],
                            'type'=>6,
                            'status'=>$list['status']
                        );
                        $aves=Db::table('pk_message')->insert($ads);
                        $ads2=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位到期提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                            'createtime'=>$minutes,
                            'updatetime'=>$minutes,
                            'endtime'=>$list['endtime'],
                            'type'=>4,
                            'status'=>$list['status']
                        );


                        $aves2=Db::table('pk_message')->insert($ads2);
//                        $aves3=Db::table('pk_message')->insert($ads5);
                    }else if($time<$list['endtime']){
                        $ads=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'系统消息',
                            'message'=>"您租用的{$manager['mg_name']}{$list['p_number']}车位租用成功",
                            'createtime'=>$list1['starttime'],
                            'updatetime'=>$list1['starttime'],
                            'endtime'=>$list['endtime'],
                            'type'=>6,
                            'status'=>$list['status']
                        );
                        $ads2=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位到期提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                            'createtime'=>$minutes,
                            'updatetime'=>$minutes,
                            'endtime'=>$list['endtime'],
                            'type'=>4,
                            'status'=>$list['status']
                        );
                        $aves=Db::table('pk_message')->insert($ads);
                        $aves2=Db::table('pk_message')->insert($ads2);
                    }else if($time >$list['endtime']){
                        $ads=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'系统消息',
                            'message'=>"您租用的{$manager['mg_name']}{$list['p_number']}车位租用成功",
                            'createtime'=>$list1['starttime'],
                            'updatetime'=>$list1['starttime'],
                            'endtime'=>$list['endtime'],
                            'type'=>6,
                            'status'=>$list['status']
                        );
                        $ads2=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位到期提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位即将在{$aa['endtime']}到期。",
                            'createtime'=>$minutes,
                            'updatetime'=>$minutes,
                            'endtime'=>$list['endtime'],
                            'type'=>4,
                            'status'=>$list['status']
                        );
                        $ads3=array(
                            'uid'=>$uid,
                            'mg_id'=>$list['mg_id'],
                            'p_number'=>$list['p_number'],
                            // 'end_time'=> $list['endtime'],
                            'title'=>'车位过期提醒',
                            'message'=>"请注意，您租用的{$manager['mg_name']}{$list['p_number']}车位已过期。",
                            'createtime'=>$time,
                            'updatetime'=>$time,
                            'endtime'=>$list['endtime'],
                            'type'=>5,
                            'status'=>$list['status']
                        );
                        $aves=Db::table('pk_message')->insert($ads);
                        $aves2=Db::table('pk_message')->insert($ads2);
                        $maves=Db::table('pk_message')->insert($ads3);
                    }
                }
            }
        }
        $data=Db::table('pk_message')->where('uid',$uid)->order('id desc')->limit($start, $num)->select();
        foreach ($data as $k=>$v){
            $data[$k]['updatetime']=date('Y-m-d H:i',$v['updatetime']);
            $data[$k]['createtime']=date('Y-m-d H:i',$v['createtime']);
        }
        $total=DB::table('pk_message')->where('uid',$uid)->count();
        $maxpage=ceil($total/$num);
        return json_encode(['code'=>1000,'data'=>$data,'maxpage'=>$maxpage,'msg'=>'操作成功']);
    }
    //车位到期提醒
    public function datelinemsg(){
        $uid=input('uid');
        $time=time();
        $data=[];$shuju=[];
        $data1=DB::table('pk_carpos')->alias('c')
        ->join('__MANAGER__ m','c.mg_id=m.mg_id')
        ->where(['c.uuid'=>$uid,'c.uuuid'=>0,'c.types'=>3])->whereor("c.uuuid=$uid")->field('c.id,m.name,c.number,end_time,end_time1')->select();
        foreach($data1 as $k=>$v){
            if(!empty($v['end_time1'])){
                if($time-$v['end_time1']<=(15*60)){
                    //共享车位还有15分钟就要到期了
                    $data[]=array(
                        'id'=>$v['id'],
                        'name'=>$v['name'],
                        'number'=>$v['number'],
                        'flag'=>2,
                        'end_time'=>date('Y-m-d H:i',$v['end_time1']));
                }/*else{
                    $data[]=array(
                        'id'=>$v['id'],
                        'name'=>$v['name'],
                        'number'=>$v['number'],
                        'flag'=>2,
                        'end_time'=>date('Y-m-d H:i',$v['end_time1']));
                }*/
            }else{
                if($time-$v['end_time']<=(15*60)){
                    //共享车位还有15分钟就要到期了
                    $data[]=array(
                        'id'=>$v['id'],
                        'name'=>$v['name'],
                        'number'=>$v['number'],
                        'falg'=>2,
                        'end_time'=>date('Y-m-d H:i',$v['end_time']));
                }/*else{
                    $data[]=array(
                        'id'=>$v['id'],
                        'name'=>$v['name'],
                        'number'=>$v['number'],
                        'flag'=>2,
                        'end_time'=>date('Y-m-d H:i',$v['end_time']));
                }*/
            }
        }
        return json_encode(['code'=>1000,'data'=>$data,'msg'=>'第一页信息获取成功']);
//        $data2=DB::table('pk_carpos')->alias('c')
//        ->join('__MANAGER__ m','m.mg_id=c.mg_id','left')
//        ->where(['uuid'=>$uid,'types'=>2])
//        ->whereOr("uuid=$uid And uuuid!=0 ANd types=3")
//        ->field('c.id,m.name,c.number,end_time')
//        ->select();
//        foreach($data2 as $k=>$v){
//            if($time-$v['end_time']<=(24*60*30)){
//                //出租车位还有1个小时就要到期了
//                $shuju[]=array(
//                    'id'=>$v['id'],
//                    'name'=>$v['name'],
//                    'number'=>$v['number'],
//                    'flag'=>1,
//                    'end_time'=>date('Y-m-d',$v['end_time'])
//                );
//            }/*else{
//                $shuju[]=array(
//                    'id'=>$v['id'],
//                    'name'=>$v['name'],
//                    'number'=>$v['number'],
//                    'flag'=>1,
//                    'end_time'=>date('Y-m-d',$v['end_time'])
//                );
//            }*/
//        }
//        $info=array_merge($data,$shuju);
//        dump($info);
    }
}
