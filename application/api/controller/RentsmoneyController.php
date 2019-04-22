<?php

namespace app\api\controller;

use think\Controller;
use think\Db;

class RentsmoneyController extends Controller
{
    public function smallpay($order_id){
       //更改状态
       Db::startTrans();
        try{
            $order_res = Db::table('pk_rentorder')->where(['order_id'=>$order_id])->find();
            if(empty($order_res)){
                Db::rollback();
                return 2;
            }
            $data = [
                'status'=>1
            ];
            $res = Db::table('pk_rentorder')->where(['order_id'=>$order_id])->update($data);
            if(empty($res)){
                Db::rollback();
                return 2;
            }
            $uuid=$order_res['uid'];
            $shuju['account']=$order_res['price'];
            $shuju['duration']=time();
            $shuju['timelong']=$order_res['timelong'];
            $shuju['uid']=$uuid;
            $flag=Db::table('pk_carpos')->where(['id'=>$order_res['pid']])->field('duration,mg_id,owner,charge1,uid,uuid,uuuid')->find();
            if(!empty($flag)){
                if($flag['owner']==1){
                    //此时表示是物业车位 
                    if(!empty($flag['charge1'])){
                        //此时表示该车位已被租用但被用户共享了
                        $data['uuuid']=$uuid;
                        $shuju['reason']=2;
                        $shuju['type']=2;
                        $shuju['oid']=$flag['uuid'];
                        $shuju['mg_id']=$flag['mg_id'];
                        $r=Db::table('pk_user')->where(['uid'=>$flag['uuid']])->setInc('moneybag',$shuju['account']);
                        if(empty($r)){
                            Db::rollback();
                            return 3;
                        }
                        $r1=Db::table('pk_user')->where(['uid'=>$uuid])->setDec('moneybag',$shuju['account']);
                        if(empty($r1)){
                            Db::rollback();
                            return 3;
                        }
                    }else{
                        $data['uuid']=$uuid;
                        $shuju['reason']=1;
                        $shuju['type']=1;
                        $shuju['oid']=$flag['mg_id'];
                        $shuju['mg_id']=$flag['mg_id'];
                        $r=Db::table('pk_manager')->where(['mg_id'=>$flag['mg_id']])->setInc('moneybag',$shuju['account']);
                        if(empty($r)){
                            Db::rollback();
                            return 3;
                        }
                        $r1=Db::table('pk_user')->where(['uid'=>$uuid])->setDec('moneybag',$shuju['account']);
                        if(empty($r1)){
                            Db::rollback();
                            return 3;
                        }
                    }
                }else if($flag['owner']==2){
                    //此时表示是个人车位
                    if(!empty($flag['charge1'])){
                        //此时表示是个人车位但是被共享了
                        $data['uuuid']=$uuid;
                        $shuju['reason']=2;
                        $shuju['type']=2;
                        $shuju['oid']=$flag['uuid'];
                        $shuju['mg_id']=$flag['mg_id'];
                        $r=Db::table('pk_user')->where(['uid'=>$flag['uuid']])->setInc('moneybag',$shuju['account']);
                        if(empty($r)){
                            Db::rollback();
                            return 3;
                        }

                        $r1=Db::table('pk_user')->where(['uid'=>$uuid])->setDec('moneybag',$shuju['account']);
                        if(empty($r1)){
                            Db::rollback();
                            return 3;
                        }
                    }else{
                        $data['uuid']=$uuid;
                        $shuju['reason']=1;
                        $shuju['type']=1;
                        $shuju['oid']=$flag['uid'];
                        $shuju['mg_id']=$flag['mg_id'];
                        $r=Db::table('pk_user')->where(['uid'=>$flag['uid']])->setInc('moneybag',$shuju['account']);
                        if(empty($r)){
                            Db::rollback();
                            return 3;
                        }
                        $r1=Db::table('pk_user')->where(['uid'=>$uuid])->setDec('moneybag',$shuju['account']);
                        if(empty($r1)){
                            Db::rollback();
                            return 3;
                        }
                    }
                }
                $shu=[
                            'account'=>$shuju['account'],
                            'duration'=>$shuju['duration'],
                            'reason'=>$shuju['reason'],
                            'type'=>$shuju['type'],
                            'oid'=>$shuju['oid'],
                            'uid'=>$shuju['uid'],
                            'timelong'=>$shuju['timelong'],
                            'pay'=>3,
                            'mg_id'=>$shuju['mg_id'],
                        ];
                $res1=Db::table('pk_revenue')->insert($shu);
                if(empty($res1)){
                    Db::rollback();
                    return 3;
                }
                //dump($data);die;
                $data['status']=1;
                $data['type']=2;
                $res2=Db::table('pk_carpos')->where(['id'=>$order_res['pid']])->update($data);
                if(empty($res2)){
                    Db::rollback();
                    return 3;
                }  
            }

            Db::commit();
            return json_encode(['code'=>1000,'msg'=>'零钱支付成功']);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
    }
}
