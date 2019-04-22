<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;

class AlitixianController extends Controller
{
/**
 * @param $out_biz_no 编号
 * @param $payee_account 提现的支付宝账号
 * @param $amount 转账金额
 * @param $payee_real_name 账号的真实姓名
 * @return bool|Exception
 */
public static function userWithDraw(
    $out_biz_no,$payee_account,$amount,$payee_real_name,$type)
{
    $payer_show_name = '提现';
    $remark = '提现到支付宝';
    vendor('alipay.aop.AopClient');
    $aop = new \AopClient();
    $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';//支付宝网关 https://openapi.alipay.com/gateway.do这个是不变的
    $aop->appId = config('alipay.appId');//商户appid 在支付宝控制台找
    $aop->rsaPrivateKey = config('alipay.rsaPrivateKey');//私钥 工具生成的 
    $aop->alipayrsaPublicKey=config('alipay.alipayrsaPublicKey');//支付宝公钥 上传应用公钥后 支付宝生成的支付宝公钥
    $aop->apiVersion = '1.0';
    $aop->signType = 'RSA2';
    $aop->postCharset='utf-8';
    $aop->format='json';
    vendor('alipay.aop.request.AlipayFundTransToaccountTransferRequest');
    $request = new \AlipayFundTransToaccountTransferRequest();
    $request->setBizContent("{" .
        "\"out_biz_no\":\"$out_biz_no\"," .
        "\"payee_type\":\"ALIPAY_LOGONID\"," .
        "\"payee_account\":\"$payee_account\"," .
        "\"amount\":\"$amount\"," .
        "\"payer_show_name\":\"$payer_show_name\"," .
        "\"payee_real_name\":\"$payee_real_name\"," .
        "\"remark\":\"$remark\"" .
        "}");
    $result = $aop->execute ($request);

    $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
    $resultCode = $result->$responseNode->code;

    if(!empty($resultCode)&&$resultCode == 10000){
        //提现成功以后 更新表状态 
        //并且记录 流水等等
        //更改状态
       Db::startTrans();
        try{
            $data=Db::table('pk_tixian')->where(['order_id'=>$out_biz_no])->find();
            if($type==1){
                //此时表示物业提现
                $res=Db::table('pk_manager')->where(['mg_id'=>$data['mg_id']])->setDec('moneybag',$amount);
            }else if($type==2){
                //此时表示用户提现
                $res=Db::table('pk_user')->where(['uid'=>$data['uid']])->setDec('moneybag',$amount);
            }
            if(empty($res)){
                Db::rollback();
                return false;
            }
            $data = [
                'is_pay'=>1
            ];
            $res = Db::table('pk_tixian')->where(['id'=>$data['id']])->update($data);
            if(empty($res)){
                Db::rollback();
                return false;
            }
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        } 
        return true;
    } else {
        //$result->$responseNode->sub_msg 这个参数 是返回的错误信息 
       throw new Exception($result->$responseNode->sub_msg);
    }
}
}
