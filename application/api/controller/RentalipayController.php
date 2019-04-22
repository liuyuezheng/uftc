<?php

namespace app\api\controller;

use think\Controller;
use think\Request;

class RentalipayController extends Controller
{
    /**
     * 租车支付宝支付
     */
    public function alipay_pay($order_num) {
       
        $res = Db::table('pk_rentorder')->where(['order_id'=>$order_num])->find();
       
        if(empty($res)){
            return json_encode(['code'=>1001,'msg'=>'订单错误']);
        }
        if($res['types']==1){
            $name = '租用出租车位';
        }else if($res['types']==2){
            $name = '租用共享车位';
        }
        $order_num = $res['order_id'];
        //获取价格
        $price = $res['price'];
        $price = 0.01;

        vendor('alipay.aop.AopClient');
        $aop = new \AopClient;
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = "2017091808802313";
        $aop->rsaPrivateKey = 'MIIEowIBAAKCAQEAzOB6o3IDuQp4puem5wrLYHLOZldMQc97YEdxz9Fj8Bzy5/Ncc5qYZfGZanJAeMkrGn0r8z/8jO9dC4Q4ui4x4yRW33ZqmdI7fNmucNOnh/7G4cWUjqxJKXXmQQGxN/+GOnuwGoZirVtLwwRRxj0uDRHqYH/7EoNt+KvEuFoThZLHycnwLoxt+7FX5T0GrzhSkbQh4i8M/lF/bQcohkLWvIwyKmJWDYVXLwBmv2GNBwX54GkpjlOX4lo3l+aJsKiVL/6+3YPGI9Yuoaw8GVOiPM60C2Vr4HnU+Qmn8/m2QNQ2pQtz15HbbAePxh6qyQSgBMjrt2s6O9j7LZ41SAna2wIDAQABAoIBAQCQSH7V4JOymydBE9880yNLd07YUB6KMl6G/Ymve51QGnMO2xp+557wHGeYyYGSDspmS0TKeIOZlXEHjUSOCb5kYtEzaqfEUIRIdt0c5FIVul3B3m2y1K5pnnhby59M+o1DXpw08fNIdwUyADa+z5NA7R8MetUMwraN7BoMYpNg+y0Oj/11LpYlXrKhQyR+c42fWn1L2yIGTmLXzK716PcTLsjDYDoStDpgqooPORpopo3ADNQUdDFgjdriiJUOQMiownEiKwpP858yvG0OwB/w5jq5WR7/pmvIeKUczDXJkolD5BwwyP7phdJ6CdL8CtZB21WaYgA6uTXjyeKZLb1BAoGBAO8HbSOf+JcqO1EA3NlemRy0gECF1b2Iqqscih8pJkL6UmVwcBjQb+7zZvpvPvM5OiEHg2x/1f+G0c6Zo6rHMtHjVc3Tzl6Q2I7RvmnsYx1tU/3k2oAL7M7zL2HkDmLIKCGNe63zNvf1lT0odLiV7hUmfBjOMPFq2PsxFKJ3rVrRAoGBANtsTlVR12mBTqr7/pzh6Ro5w7ZfTvMtdEgr+M+1ZHp8wqWcSdLneZiHLxb+7QAB/vlNiku1k4njQ3rAXRNNC6DVygW5Oczn695dhqHeaiRQtx3aYH+fOoachPfyP5k9Aro8Tlr7H3xQeFuDct+QRdIHK4kvwtQT351+oTYOlO3rAoGADK5zLuGs2bBG51xJW0r2ipxU9ZdkKKMYku13soGHYyROvM0DVX2xgpbtTroaN+NAX0I7ycTagK0RcomaMlRRMOuDwODM4R2EL8eW952wAH6tZxn+Ma7wSGaEjAgCb2E5J9aOykLOFsezvEPqNWTW9c5N5S8DT7ugeWs4MgpxaxECgYBBVZx1dysG9UOxUdtcZz/7WRvXX8WoTu6C1uT9I+vJNQDYQxMQQ3BHZGk3Fa0IBZAgN2BobqaBtjPPhxuvtY8y0rWWwrJdOulWis6dwBYmvgnoT6/QEF9i2ZQWKAGb5Ti8r1w9ZuzXHTbZOOipfNHtWckyzg/bChfZU205JVpfBQKBgGBKEHhZABc5BLTQGrLWOszsDco7uqw8pwBvJFwrySLNXU4tmeMcNerr5WN1+Ezi/YDa1Fi5YQUKCsNz6/e8EYN8VizTWiFAzXGkOTi+1tlYv7TSKrXQUWu0agZWL3cO/oHvEkWzEP0+bAFlXu61oB5cnIlDJ9uIGp6ufYO0Qqoh';
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmrbfwzwYVUfRzaw2OUlRn4l7u6s31JDazGv0y1dmW5VJ1sCXW3r4LIJ5H9U9OBxx8rqbm7BTK//yfPFMyVFmVNzui+HR5InBNScR2eMX2eVj16Lxwq4TSnD6ZgoY9CsziwApa3W/mDF4hZmX7DKIaPWsW83WQ/BZPNndovMMT7t+vKG2Jqe6L9UlYGKzcii3OGH4Y5UpWLaQGIUwYyXZw/CGsDYGrA4cmUzUEORdcMOF106tEnZtm7mvHMrvX5SGH6kGaumOW3q5aMMJ8IUNnYvR/FQ2XFlcapbVtD3Zw2nvj2dZGH6OzBli6apJQTsIKYUVv5oRojhd6U1SsfiVJQIDAQAB';
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        vendor('alipay.aop.request.AlipayTradeAppPayRequest');
        $request = new \AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent = "{\"body\":\"\","
            . "\"subject\": \"" . $name . "\","
            . "\"out_trade_no\": \"" . $order_num . "\","
            . "\"timeout_express\": \"30m\","
            . "\"total_amount\": \"".$price."\","
            . "\"product_code\":\"QUICK_MSECURITY_PAY\""
            . "}";
        $notify_url = 'http://www.park.com/api/Rentalipay/alipay_callback';
        $request->setNotifyUrl($notify_url);
        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);//htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
        return json_encode(['code'=>1000,'data'=>$response,'msg' =>'数据获取成功']);

        // return return_json_format_return($data);
    }
    /**
     *支付宝支付成功回调
     */
    public function alipay_callback() {
        vendor('alipay.aop.AopClient');
        $aop = new \AopClient;
        $aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmrbfwzwYVUfRzaw2OUlRn4l7u6s31JDazGv0y1dmW5VJ1sCXW3r4LIJ5H9U9OBxx8rqbm7BTK//yfPFMyVFmVNzui+HR5InBNScR2eMX2eVj16Lxwq4TSnD6ZgoY9CsziwApa3W/mDF4hZmX7DKIaPWsW83WQ/BZPNndovMMT7t+vKG2Jqe6L9UlYGKzcii3OGH4Y5UpWLaQGIUwYyXZw/CGsDYGrA4cmUzUEORdcMOF106tEnZtm7mvHMrvX5SGH6kGaumOW3q5aMMJ8IUNnYvR/FQ2XFlcapbVtD3Zw2nvj2dZGH6OzBli6apJQTsIKYUVv5oRojhd6U1SsfiVJQIDAQAB';
        $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");
        $flag_str = json_encode($flag);
        if ($flag) {
            if(!empty($_POST['out_trade_no'])){

                 file_put_contents('2.txt',$_POST['out_trade_no']);
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
                        $data['type']=2;//表示租赁中
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
