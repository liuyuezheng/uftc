<?php

namespace app\api\service;

use think\Db;
use think\Request;
use think\Loader;
use app\api\model\Rent;
use app\api\model\Recharges;

class PayService
{
    public static function aliPay($orderId = null)
    {
        $name = strpos($orderId, 'c') !== false ? '钱包充值': '购买车位';
        $price = '0.01';
        if ($name === '购买车位') {
            $renModel = new Rent();
            $orderInfo = $renModel->getOrder($orderId);
        } else {
            $rechargesModel = new Recharges();
            $orderInfo = $rechargesModel->getOrder($orderId);
        }

        if (empty($orderInfo)) {
            return false;
        }
        vendor('alipay.aop.AopClient');
        $aop = new \AopClient;
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = "2018121362531610";
        $aop->rsaPrivateKey = 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDT1o1aSfcFRxAsGDS/gFZ2lUcWKb0xHf/c7rw994kED7dQsIlfcgM8ypknd9ZVlthKs6DHcgOQWvdXsaLz8yRYQrC065xmKbD+bqkku1OQ62BCJckRYohNqXfDSbU+BmWbxplKBACf4UWLoXbszQStfx7NUiCGwyxdito0QSz3yshc3PmH0jACUgc2/+XYrWGqoxTGr4v4MJvZgXsnztKkEz0pNivrw/ll9B0vAaU3hM3l0jCBzPd1SlwbZiQyq9kcHaCHaZ6Fp81C9/oP8Zhc6UasrV9EPqO0cYv+1wK5ZXAeSR1KtrXC7hjuAODfwnxAyHR/LwqRshBSGlXfnIprAgMBAAECggEBAKiIzbTDm8+LFga+y1iAFkoJwaCUXHyzTVjMZt82DMA6cLG1gdV5s7GtzeNkqBU4CaSE/KkwwD0c4iPWo7pJ+uRtSoDl4mw1RydlixQ6JOXrHY4C2dEG/0IqTaoE/jj2hehFPLdyL70DUH0iXKdRBSOzOyJG8LHGnYleavOf1tLcA3WROmkPYJfnU8wnUDlsrTksma/d75sh8vbok0m5W9pGOpNutZVj29NlNsnQWykSc+sZLrvh0yw2U4Ej6JpkIeu0NM0FWWcyuZzEIZe2FFDT3hp77RrGEOZqHqYQnNC21lJm2y+BMm+Zsx6Q64AT0Egui0ldWWALcy0q+EWqESECgYEA8cWUb6L4cG+GZrvoaQHPRc41lr9afttdncOMSex453QYvzR4ZL/ZMJ3YiprN/AzBPg3yydUzLt08KS4eIJ0bJzFTxsW3kRS1d+vrw7ItIJ8UhWYVkKVGzF6/uM8EhPVvPDUVuF1537C2o61CGCQsLtiKsMWW/FZBGbTI1cVuAJUCgYEA4E4BWIdgs9AcMHXGGZj/vPlO8F37tsrDVGCR+5XPsfC1q9hgK+bWkcFx4I1fGTP3heDhuqq2bCkp82pjKrHXppYDpaCmxH9mjjfkGNlXIogJqzqQTo8BXi0+AYjzGSVqD35MK+HYStzqH8zpZDuetM8POMG9v/ZTd/7+jlZ2nv8CgYEA16oDisG5E7KoFSQxYNn4ZrBXJS10MAzGKWCx149VGkF5gbXwXw6zUqa67ojAjcFi71PJ6zh++6Llc2ZsAXOjMQbTUFA2OvhaF6sfF+XsU1kUGRrCydBkoxExvE0OHvxASVihE2BuwpcDlGWMu7QopXEL5jubP2RBlgipzebbBTECgYAqbtdWVhX3LlGG4WDeitEmgMtsMXti8yzMk5BKVeb6tJzJq59V7s98t2nBnzz0WZ5j100csRrdj1P+Vov4EbjILOz3slKaLbfLA8vE36jaQX4CxIt0MR2DIW/vfmbuxOMxlyT10D3Iu84Wwf8NFTJK5jjs9cndvM3Bj528c6LxJQKBgFABWgJ63odvGPslqPp8dfb0UnkoKfgDKTCR0SJIkhDxRP9URi9a659VINWjkHWJgGZ0J1EyUfxEHUPu1rJpUY3nPJsXs7+sEvDRzUBIv4VRg3sh8+MwgG+FMlWFVk4Xi1mn37GFuiyJEPUI5EHxZgCE99sf9yDvWwNAQg8CpQpm';
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->alipayrsaPublicKey = '
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA09aNWkn3BUcQLBg0v4BWdpVHFim9MR3/3O68PfeJBA+3ULCJX3IDPMqZJ3fWVZbYSrOgx3IDkFr3V7Gi8/MkWEKwtOucZimw/m6pJLtTkOtgQiXJEWKITal3w0m1PgZlm8aZSgQAn+FFi6F27M0ErX8ezVIghsMsXYraNEEs98rIXNz5h9IwAlIHNv/l2K1hqqMUxq+L+DCb2YF7J87SpBM9KTYr68P5ZfQdLwGlN4TN5dIwgcz3dUpcG2YkMqvZHB2gh2mehafNQvf6D/GYXOlGrK1fRD6jtHGL/tcCuWVwHkkdSra1wu4Y7gDg38J8QMh0fy8KkbIQUhpV35yKawIDAQAB';
        vendor('alipay.aop.request.AlipayTradeAppPayRequest');
        $request = new \AlipayTradeAppPayRequest();
        $bizcontent = "{\"body\":\"\","
            . "\"subject\": \"" . $name . "\","
            . "\"out_trade_no\": \"" . $orderId . "\","
            . "\"timeout_express\": \"30m\","
            . "\"total_amount\": \"".$price."\","
            . "\"product_code\":\"QUICK_MSECURITY_PAY\""
            . "}";
        $notify_url = 'http://park.mumarenkj.com/api/rent/alipayCallBack';
        $request->setNotifyUrl($notify_url);
        $request->setBizContent($bizcontent);
        $response = $aop->sdkExecute($request);
        return $response;
    }

    public static function wxPay($orderId = null)
    {
        $renModel = new Rent();
        $orderInfo = $renModel->getOrder($orderId);
        if (empty($orderInfo)) {
            return false;
        }

        $price =0.1;
        $name = '租用出租车位';
        $type = 1;
        vendor('appwechatpay.lib.WxPayApi');
        $time = time();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($name);
        $input->SetOut_trade_no($orderId);
        // $input->SetOut_trade_no('');
        $input->SetTotal_fee($price * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", $time + 1800));
        $input->SetTrade_type("APP");
        $input->SetAttach($type);

        $notify_url = 'http://www.park.com/api/Rentwxpay/wxpay_callback';

        $input->SetNotify_url($notify_url);
        $order_param = \WxPayApi::unifiedOrder($input);
        return $order_param;
    }
}

