<?php

namespace app\api\controller;

use app\api\model\Rent;
use app\api\validate\ParkListValidate;
use app\api\service\TokenService;
use think\Db;

class RentController extends BaseController
{
    protected $ParkListValidate = null;
    protected $orderId = null;
    protected $url = 'park.mumarenkj.com';
    protected $RentModel = null;

    public function __construct()
    {
        // 制定允许其他域名访问
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with, content-type');
        $this -> ParkListValidate = new ParkListValidate();
        $this -> RentModel = new Rent();
    }

    /**
     * 获取出租共享车位列表
     */
    public function parkList()
    {
        $param = request()->only(['types','flag','longitude','latitude']);
        $this->ParkListValidate->scene('list', ['types','flag','longitude','latitude']);
        $result = $this->ParkListValidate->scene('list')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->ParkListValidate->getError());
        }

        $types = empty($param['types']) ? 2 : $param['types'];
        $flag =  empty($param['flag'])  ? 1 : $param['flag'];
        $page =  empty($param['page'])  ? 1 : $param['page'];
        $num = 15;

        $data = $this -> RentModel->getList($types, $flag, $param['longitude'], $param['latitude']);
        $data = getPageData($data, $num, $page);
        return success(1000, $data, '获取出租共享消息列表成功');
    }

    /**
     * 出租详情
     */
    public function rentDetail()
    {
        $param = request()->only(['id']);
        $this->ParkListValidate->scene('id', ['id']);
        $result = $this->ParkListValidate->scene('id')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->ParkListValidate->getError());
        }
        $types = 2;
        $data = $this->RentModel->getDetail($param['id'], $types);
        return json_encode(['code'=>1000, 'data'=>$data['result'], 'images'=>$data['images'], 'msg'=>'出租车位信息获取成功']);
    }

    /**
     * 共享详情
     */
    public function shareDetail()
    {
        $param = request()->only(['id']);
        $this->ParkListValidate->scene('id', ['id']);
        $result = $this->ParkListValidate->scene('id')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->ParkListValidate->getError());
        }

        $types = 3;
        $data = $this->RentModel->getDetail($param['id'], $types);

        return json_encode(['code'=>1000, 'data'=>$data['result'], 'images'=>$data['images'], 'msg'=>'出租车位信息获取成功']);
    }

    /**
     * 支付
     */
    public function clickPay()
    {
        $param = request()->only(['orderId','uid']);
        $this ->ParkListValidate->scene('pay', ['orderId','uid']);
        $result = $this ->ParkListValidate->scene('pay')->check($param);
        if (true !== $result) {
            return success(1001, '', $this->ParkListValidate->getError());
        }

        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }

        $data = $this->RentModel->getOrder($param['orderId']);

        $this->RentModel->editOwner($data['pid']);

        $userInfo = $this->RentModel->getUser($param['uid']);

        if ($userInfo['moneybag']-$data['price'] < 0) {
            return success(1005, '', '余额不足');
        }

        if ($data['status'] == 1) {
            return success(1003, '', '已经支付过的订单');
        }

        $is_pay = $this->RentModel->changePrice($data);

        if ($is_pay) {
            $changeRes = $this->RentModel->changeCarpos($data);
            if ($changeRes) {
                return success(1000, '', '租赁成功');
            }
        }

        return success(1004, '', '租赁失败');
    }
    public function clickUse()
    {
        $param = request()->only(['uid','pid']);
        $this ->ParkListValidate->scene('use', ['uid','pid']);
        $result = $this ->ParkListValidate->scene('use')->check($param);
        if (true !== $result) {
            return success(1001, '', $this ->ParkListValidate->getError());
        }

        if (!TokenService::checkUserId($param['uid'])) {
            return success(1002, '', '无效token');
        }

        $where = [];
        $where['id'] = ['eq', $param['pid']];
        $where['status'] = ['eq', 2];

        $join = [
           ['__MANAGER__ m','m.mg_id = c.mg_id']
        ];

        $field = 'c.uid,c.uuid,c.uuuid,c.types,c.number,c.mg_id,c.charge1,c.charge,c.end_time1,c.end_time,c.start_time1,c.start_time';

        $carInfo = Db::table('pk_carpos')->alias('c')->where($where)->join($join)->field($field)->find();
        if (empty($carInfo)) {
            return success(1004, '', '车位发生变化');
        }




        $time = ($carInfo['types'] == 3) ?  3600: 3600*30*24;
        $startTime = ($carInfo['types'] == 3) ? $carInfo['start_time1'] : $carInfo['start_time'];
        $endTime = ($carInfo['types'] == 3) ? $carInfo['end_time1'] : $carInfo['end_time'];

        $charge = ($carInfo['types'] == 3) ? $carInfo['charge1'] : $carInfo['charge'];

        $tatolPrice = ($charge)*(($endTime-$startTime)/$time);
        $data=[
            'order_id'=>date('YmdHis') . str_pad(mt_rand(1, 999999999999), 5, '0', STR_PAD_LEFT),
            'uid'=>$param['uid'],
            'dateline'=>date('Y/m/d H:i:s', time()),
            'pid'=>$param['pid'],
            'number'=>$carInfo['number'],
            'mg_id'=>$carInfo['mg_id'],
            'status'=>2,
            'price'=>$tatolPrice,
            'charge'=>$charge,
            'types'=>$carInfo['types'],
            'start_time'=>$startTime,
            'end_time'=>$endTime,
        ];

        if ($carInfo['types'] == 3 && $carInfo['uuid']==0) { //个人车位直接分享
            $data['receiptsId'] = $carInfo['uid'];
            $data['receiptsType'] = 1;
        } elseif ($carInfo['types'] == 3 && $carInfo['uuid']!=0) {//出租车位->分享
            $data['receiptsId'] = $carInfo['uuid'];
            $data['receiptsType'] = 1;
        } elseif ($carInfo['types'] == 2 && $carInfo['uid']!=0) {//个人车位->出租
            $data['receiptsId'] = $carInfo['uid'];
            $data['receiptsType'] = 1;
        } elseif ($carInfo['types'] == 2 && $carInfo['uid']==0) {//物业车位->出租
            $data['receiptsId'] = $carInfo['mg_id'];
            $data['receiptsType'] = 2;
        }

        $result = Db::table('pk_rentorder')->insert($data);
        if ($result) {
            return success(1000, ['orderId' => $data['order_id']], '生成订单成功');
        } else {
            return success(1003, '', '生成订单失败');
        }
    }
    public function getOrder(){
    $param = request()->only(['orderId','uid']);

    $this ->ParkListValidate->scene('order', ['orderId','uid']);
    $result = $this ->ParkListValidate->scene('order')->check($param);
    if (true !== $result) {
        return success(1001, '', $this ->ParkListValidate->getError());
    }

    if (!TokenService::checkUserId($param['uid'])) {
        return success(1002, '', '无效token');
    }

    $where['r.order_id'] = ['eq', $param['orderId']];
    $where['c.status'] = ['eq', 2];


    $join = [
        ['carpos c','r.pid = c.id'],
        ['__MANAGER__ m','m.mg_id = c.mg_id']

    ];

    $orderInfo = Db::table('pk_rentorder')
        ->alias('r')
        ->join($join)
        ->where($where)
        ->field('r.price,r.pid,r.start_time,r.end_time,r.charge,m.name,m.address,c.number,r.types')
        ->find();



    if(empty($orderInfo) || !$this->checkOrder($orderInfo)){
        return success(1003, '' ,'订单信息发生变化或已支付');
    }

    if ($orderInfo['types'] == 3) {
        $time = date('Y/m/d', $orderInfo['start_time']).' '.date('H:i', $orderInfo['start_time']).'-'.date('H:i', $orderInfo['end_time']);
    } else {
        $time = date('Y/m/d H:i', $orderInfo['start_time']).'-'.date('Y/m/d H:i', $orderInfo['end_time']);
    }
    $data=[
        'name'=>$orderInfo['address'].$orderInfo['name'],
        'pid'=>$orderInfo['pid'],
        'number'=>$orderInfo['number'],
        'price'=>$orderInfo['price'],
        'types'=>$orderInfo['types'],
        'time'=>$time,
    ];

    return success(1000, $data, '获取订单成功');

}
   public function checkOrder($orderInfo){
        $carInfo = Db::table('pk_carpos')->where('id', $orderInfo['pid'])->find();


        if ($carInfo['types'] != $orderInfo['types']) {
            return false;
        }

        $startTime = ($carInfo['types'] == 2) ? 'start_time' : 'start_time1';
        $endTime = ($carInfo['types'] == 2) ? 'end_time' : 'end_time1';
        $charge = ($carInfo['types'] == 2) ? 'charge' : 'charge1';
        if ($carInfo[$startTime] != $orderInfo['start_time'] || $carInfo[$endTime] != $orderInfo['end_time'] || $carInfo[$charge] != $orderInfo['charge']) {
            return false;
        }

        return true;
    }


   

   //支付成功之后()
   public function payaccomplish(){
        $id=input('id');
        $uuid=input('uid');
        $shuju['account']=input('account');
        $shuju['duration']=time();
        $shuju['timelong']=input('timelong');
        $shuju['pay']=input('pay');//1:支付宝2：微信3：零钱
        $shuju['uid']=$uuid;
        $flag=Db::table('pk_carpos')->where(['id'=>$id])->field('duration,mg_id,owner,charge1,uid,uuid,uuuid')->find();
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
            }else{
                $data['start_time']=time();
                $data['end_time']=strtotime("+".$flag['duration']." month");
                $data['uuid']=$uuid;
                $shuju['reason']=1;
                $shuju['type']=1;
                $shuju['oid']=$flag['mg_id'];
                $shuju['mg_id']=$flag['mg_id'];
                $r=Db::table('pk_manager')->where(['mg_id'=>$flag['mg_id']])->setInc('moneybag',$shuju['account']);
            }
        }else if($flag['owner']==2){
            //此时表示是个人车位
            if(!empty($falg['charge1'])){
                //此时表示是个人车位但是被共享了
                $data['uuuid']=$uuid;
                $shuju['reason']=2;
                $shuju['type']=2;
                $shuju['oid']=$flag['uuid'];
                $shuju['mg_id']=$flag['mg_id'];
                $r=Db::table('pk_user')->where(['uid'=>$flag['uuid']])->setInc('moneybag',$shuju['account']);
            }else{
                $data['uuid']=$uuid;
                $shuju['reason']=1;
                $shuju['type']=1;
                $shuju['oid']=$flag['uid'];
                $shuju['mg_id']=$flag['mg_id'];
                $r=Db::table('pk_user')->where(['uid'=>$flag['uid']])->setInc('moneybag',$shuju['account']);
            }
        }
        $res1=Db::table('pk_revenue')->insert($shuju);
        $data['status']=1;
        $data['type']=2;//表示租赁中
        $res=Db::table('pk_carpos')->where(['id'=>$id])->update($data);//这里我需不需要穿一个参数给客户？
        if(!empty($res)){
            return json_encode(['code'=>1000,'msg'=>'车位信息更新成功']);
        }else{
            return json_encode(['code'=>1001,'msg'=>'车位信息更新失败']);
        }
   }
}
