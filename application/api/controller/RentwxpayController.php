<?php

namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Db;

class RentwxpayController extends Controller
{
    public function wx_pay($order_id = 10000001){
//        $res = Db::table('pk_stopcarorder')->where(['order_id' => $order_id])->find();
//
//        if (empty($res)) {
//            return json_encode(['code'=>1001,'msg'=>'订单错误']);
//        }
        $res['types'] = 1;
        if($res['types']==1){
            $name = '租用出租车位';
        }else if($res['types']==2){
            $name = '租用共享车位';
        }
//        $order_id = $res['order_id'];
        //$price = $res['price'];
        $price =0.1;

        $type = 1;
        vendor('appwechatpay.lib.WxPayApi');
        $time = time();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($name);
        $input->SetOut_trade_no($order_id);
        // $input->SetOut_trade_no('');
        $input->SetTotal_fee($price * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", $time + 1800));
        $input->SetTrade_type("APP");
        $input->SetAttach($type);

        $notify_url = 'http://www.park.com/api/Rentwxpay/wxpay_callback';

        $input->SetNotify_url($notify_url);
        $order_param = \WxPayApi::unifiedOrder($input);
        return json_encode(['code'=>1000, 'data'=>$order_param, 'msg'=>'微信成功调起']);
    }

    /**
     *微信支付成功回调
     */
    public function wxpay_callback(){
        vendor('appwechatpay.lib.WxPayConfig');
        $result = file_get_contents('php://input', 'r');
        $result = (array)simplexml_load_string($result, null, LIBXML_NOCDATA);
        // file_put_contents('test2.txt',$result);
        if ($result['result_code'] === 'SUCCESS' && $result['mch_id'] === \WxPayConfig::MCHID && $result['appid'] === \WxPayConfig::APPID) {
            ksort($result);
            //拼接生成签名的字符串
            $sign_string = '';
            foreach ($result as $key => $value) {
                if ($key !== 'sign') {
                    $sign_string .= $key . '=' . $value . '&';
                }
            }
            $sign_string .= 'key=' . \WxPayConfig::KEY;
            $sign = strtoupper(md5($sign_string));

            if ($sign === $result['sign']) {
                 file_put_contents('22.txt', $result['out_trade_no']);
                 //更改状态
               Db::startTrans();
                try{
                    $order_res = Db::table('pk_rentorder')->where(['order_id'=>$_POST['out_trade_no']])->find();
                    if(empty($order_res)){
                        Db::rollback();
                        return 2;
                    }
                    $data = [
                        'status'=>1
                    ];
                    $res = Db::table('pk_rentorder')->where(['order_id'=>$_POST['out_trade_no']])->update($data);
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
                        $data['status']=1;
                        $data['type']=2;
                        $res2=Db::table('pk_carpos')->where(['id'=>$order_res['pid']])->update($data);
                        if(empty($res2)){
                            Db::rollback();
                            return 3;
                        }  
                    }

                    Db::commit();
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                }
            }
        }
    }
}
