<?php

namespace app\api\controller;

use think\Controller;
use think\Db;

class IndexsmoneyController extends Controller
{
    public function smallpay($order_id){
        Db::startTrans();
        try{
            $order_res = Db::table('pk_stopcarorder')->where(['order_id'=>$order_id])->find();
            if(empty($order_res)){
                Db::rollback();
                return 2;
            }
            $data = [
                'status'=>1
            ];
            $res = Db::table('pk_stopcarorder')->where(['order_id'=>$_POST['out_trade_no']])->update($data);
            if(empty($res)){
                Db::rollback();
                return 2;
            }
            $where2 = [
                'mg_id'=>$order_res['mg_id'],
            ];
            $ma_res = Db::table('pk_manager')->where($where2)->setInc('moneybag',$order_res['price']);
            if(empty($ma_res)){
                Db::rollback();
                return 2;
            }
            $where3 = [
                'muid'=>$order_res['muid'],
            ];
            $ma_res = Db::table('pk_user')->where($where2)->setDec('moneybag',$order_res['price']);
            if(empty($ma_res)){
                Db::rollback();
                return 2;
            }
            $info['account']=$order_res['price'];
            $info['duration']=time();
            $info['reason']=3;
            $info['type']=1;
            $info['oid']=$order_res['mg_id'];
            $info['mg_id']=$order_res['mg_id'];
            $info['uid']=$order_res['uid'];
            $info['timelong']=$order_res['timelong'];
            $re_res=Db::table('pk_revenue')->insert($info);
            if(empty($re_res)){
                Db::rollback();
                return 2;
            }

            Db::commit();
            return json_encode(['code'=>1000,'msg'=>'零钱支付成功']);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
    }
}
