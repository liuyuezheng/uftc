<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class IndexwxpayController extends Controller
{
    public function wx_pay($order_id){
        $res = Db::table('pk_stopcarorder')->where(['order_id' => $order_id])->find();

        if (empty($res)) {
            return json_encode(['code'=>1001,'msg'=>'订单错误']);
        }
        $name = '临时停车';
        $order_id = $res['order_id'];
        //$price = $res['price'];
        $price =0.1;

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

        $notify_url = 'http://www.park.com/api/Indexwxpay/wxpay_callback';

        $input->SetNotify_url($notify_url);
        $order_param = \WxPayApi::unifiedOrder($input);
        return json_encode(['code'=>1000, 'data'=>$order_param, 'msg'=>'微信成功调起');
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
                 file_put_contents('11.txt', $result['out_trade_no']);
                 //更改状态
               Db::startTrans();
                try{
                    $order_res = Db::table('pk_stopcarorder')->where(['order_id'=>$_POST['out_trade_no']])->find();
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
                } catch (\Exception $e) {
                    // 回滚事务
                    Db::rollback();
                }
            }
        }
    }
}
