<?php

namespace app\api\model;

use think\Model;
use think\Db;

class Recharges extends Model
{
    public function getOrder($orderId)
    {
        return Db::table('pk_recharges')->where('order_id', $orderId)->find();
    }

    public function rechargesSuccess($orderInfo)
    {
        Db::startTrans();
        try {
            Db::table('pk_recharges')->where('order_id', $orderInfo['order_id'])->update(['status'=>1]);
            Db::table('pk_user')->where('uid', $orderInfo['uid'])->setInc('moneybag', $orderInfo['account']);
            Db::commit();
            return 'success';
        } catch (\Exception $e) {
            Db::rollback();
            return 'error';
        }
    }
}
