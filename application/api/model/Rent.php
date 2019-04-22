<?php

namespace app\api\model;

use think\Db;
use think\Model;

class Rent extends Model
{

    /**
     * 是否是个人车位
     *
     * @param $pid 车位id
     * @return bool
     */
    public function isSelf($pid)
    {
        $result = Db::table('pk_carpos')->where('id', $pid)->find();
        if (empty($result)) {
            return false;
        }
        $isSelf = ($result['uid'] != 0 && $result['owner'] == 2) ? true : false;
        return $isSelf;
    }

    /**
     * 获取出租共享列表
     *
     * @param $types 类型 2出租 3共享
     * @param $flag 排序 1距离 2价格 3租期
     * @param $longitude 精度
     * @param $latitude 维度
     * @return array
     */
    public function getList($types, $flag, $longitude, $latitude)
    {
        $order = '';
        if ($types == 3 && $flag == 2) {
            $order = 'c.charge1 asc';
        } elseif ($types == 3 && $flag == 3) {
            $order = 'c.duration1 desc';
        } elseif ($types == 2 && $flag == 3) {
            $order = 'c.duration desc';
        } elseif ($types == 2 && $flag == 2) {
            $order = 'c.charge asc';
        }

        $where = [];
        $where['c.status'] = ['eq', 2];
        $where['c.types'] = ['eq', $types];
        $where['style'] = 1;

        $isShare = ($types == 3) ? '1' : '';
        $field = 'c.id,c.number,m.mg_id,m.name,m.address,m.area,m.longitude,m.latitude,c.charge'.$isShare.' as charge,c.duration'.$isShare.' as duration';


        $join = [
            ['__MANAGER__ m', 'm.mg_id = c.mg_id', 'left']
        ];

        $result = DB::table('pk_carpos')->alias('c')->join($join)->where($where)->field($field)->order($order)->select();
        $data = listData($result, $longitude, $latitude, $types, $flag);

        return $data;
    }

    /**
     * 获取车位详情
     * @param $pid 车位id
     * @param $types 类型 2出租 3共享
     * @return array
     */
    public function getDetail($pid, $types)
    {
        $where['c.id'] = ['eq', $pid];

        $join = [
            ['__MANAGER__ m', 'm.mg_id = c.mg_id', 'left'],
        ];

        $isShare = ($types == 3) ? '1' : '';
        $filed = 'c.id,c.number,c.mg_id,m.logo,m.mg_id,c.types,m.name,m.address,m.area,c.owner,c.charge'.$isShare.' as charge,c.duration'.$isShare.' as duration,c.start_time'.$isShare.' as start_time,c.end_time'.$isShare.' as end_time,m.longitude,m.latitude,c.logo2';

        $data = DB::table('pk_carpos')->alias('c')->join($join)->where('c.id', $pid)->field($filed)->find();

        if (!empty($data)) {
            $data['duration'] = getTime($data['duration'], $types);
            $data['owner'] = ($data['owner'] == '1') ? '物业':'个人';
            $data['address'] = $data['area'].$data['address'];
            $data['start_time'] = date('Y/m/d', $data['start_time']);
            $data['end_time'] = date('Y/m/d', $data['end_time']);
            $data['show'] = getImage(explode(',,,', $data['logo2']));
            $data['time'] = $data['start_time'].'-'.$data['end_time'];
        }
        unset($data['logo2']);
        return ['result'=>$data, 'images'=>[getImage($data['logo'])]];
    }

    /**
     * 修改最上级车位status状态
     * @param $pid 车位id
     */
    public function editOwner($pid)
    {
        $where['id'] = ['eq', $pid];
        $carposInfo = Db::table('pk_carpos')->where($where)->find();
        if (!empty($carposInfo['uid'])) {
            $sql = "UPDATE `pk_carpos` SET `status`=1 WHERE `uid` = ".$carposInfo['uid']." AND `uuid` = 0 AND `uuuid` = 0 AND `start_time` is null AND `end_time` is null AND `start_time1` is null AND `end_time1` is null AND `number` = '".$carposInfo['number']."' AND `mg_id` = ".$carposInfo['mg_id'];
            Db::query($sql);
        }
    }

    /**
     * 获取个人信息 user表
     * @param $uid
     * @return array
     */
    public function getUser($uid)
    {
        $data = Db::table('pk_user')->where('uid', $uid)->find();
        return $data;
    }

    /**
     * 获取订单信息 user表
     * @param $orderId
     * @return array
     */
    public function getOrder($orderId)
    {
        $data = Db::table('pk_rentorder')->where('order_id', $orderId)->find();
        return $data;
    }

    /**
     * 修改钱包
     * @param $orderData
     * @return bool
     */
    public function changePrice($orderData)
    {
        $result = Db::table('pk_user')->where('uid', $orderData['uid'])->setDec('moneybag', $orderData['price']);
        if (!empty($result)) {
            $reInsert = [
                'account' =>$orderData['price'],
                'duration'=>time(),
                'type'=>2,
                'uid'=>$orderData['uid'],
                'mg_id'=>$orderData['mg_id'],
                'create_time'=>time(),
                'receiptsId'=>$orderData['receiptsId']
            ];
            if ($orderData['receiptsType'] == 2) {
                Db::table('pk_user')->where('uid', $orderData['receiptsId'])->setInc('moneybag', $orderData['price']);
                $reason = ($orderData['types'] == 3) ? 2 : 1;
                $reInsert['reason'] = $reason;
            } else {//收款方是物业
                Db::table('pk_manager')->where('mg_id', $orderData['receiptsId'])->setInc('moneybag', $orderData['price']);
                Db::table('pk_manager')->where('mg_id', 1)->setInc('price', $orderData['price']);
                $reason = ($orderData['types'] == 2) ? 1 : 2;
                $reInsert['reason'] = $reason;
            }

            $result = Db::table('pk_revenue')->insert($reInsert);
            if (!empty($result)) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 支付成功后修改车位状态
     * @param $orderData
     * @return bool
     */
    public function changeCarpos($orderData)
    {
        $update_arr['type'] = 0;
        $update_arr['status'] = 1;
        $update_arr['update_time'] = time();
        $update_arr['types'] = $orderData['types'];
        if ($orderData['types'] == 2) {
            $update_arr['uuid'] = $orderData['uid'];
            $update_arr['uuuid'] = 0;
            $update_arr['start_time'] = $orderData['start_time'];
            $update_arr['end_time'] = $orderData['end_time'];
        } else {
            $update_arr['start_time1'] = $orderData['start_time'];
            $update_arr['end_time1'] = $orderData['end_time'];
            $update_arr['uuuid'] = $orderData['uid'];
        }
        $result = Db::table('pk_carpos')->where('id', $orderData['pid'])->update($update_arr);

        if (!empty($result)) {
            Db::table('pk_rentorder')->where('order_id', $orderData['order_id'])->update(['status'=>1]);
            return true;
        }
        return false;
    }

    /*
     * 购买车位成功之后的回调
     */
    public function payCallBack($orderData)
    {
        Db::startTrans();
        try {
            //修改钱包状态
            $reInsert = [
                'account' =>$orderData['price'],
                'duration'=>time(),
                'type'=>2,
                'uid'=>$orderData['uid'],
                'mg_id'=>$orderData['mg_id'],
                'create_time'=>time(),
                'receiptsId'=>$orderData['receiptsId']
            ];
            if ($orderData['receiptsType'] == 2) {
                Db::table('pk_user')->where('uid', $orderData['receiptsId'])->setInc('moneybag', $orderData['price']);
                $reason = ($orderData['types'] == 3) ? 2 : 1;
                $reInsert['reason'] = $reason;
            } else {//收款方是物业
                Db::table('pk_manager')->where('mg_id', $orderData['receiptsId'])->setInc('moneybag', $orderData['price']);
                Db::table('pk_manager')->where('mg_id', 1)->setInc('price', $orderData['price']);
                $reason = ($orderData['types'] == 2) ? 1 : 2;
                $reInsert['reason'] = $reason;
            }
            Db::table('pk_revenue')->insert($reInsert);

            //修改车位状态
            $update_arr['type'] = 0;
            $update_arr['status'] = 1;
            $update_arr['update_time'] = time();
            $update_arr['types'] = $orderData['types'];
            if ($orderData['types'] == 2) {
                $update_arr['uuid'] = $orderData['uid'];
                $update_arr['uuuid'] = 0;
                $update_arr['start_time'] = $orderData['start_time'];
                $update_arr['end_time'] = $orderData['end_time'];
            } else {
                $update_arr['start_time1'] = $orderData['start_time'];
                $update_arr['end_time1'] = $orderData['end_time'];
                $update_arr['uuuid'] = $orderData['uid'];
            }
            Db::table('pk_carpos')->where('id', $orderData['pid'])->update($update_arr);
            Db::table('pk_rentorder')->where('order_id', $orderData['order_id'])->update(['status'=>1]);
            Db::commit();
            return 'success';
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return 'error';
        }
    }

}
