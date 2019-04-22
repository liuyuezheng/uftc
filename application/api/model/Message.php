<?php

namespace app\api\model;

use think\Model;

class Message extends Model
{
    public function addMessage($pid, $uid)
    {
        $carInfo = $this ->getCarInfo($pid);
        $message = '您已经成功租赁'.$carInfo['number'].'车位';
        Db::table('pk_message')->insert(['title'=>'系统消息','message'=>$message,'createtime'=>time(),'uid'=>$uid,'updatetime'=>time(),'mg_id'=>$carInfo['mg_id'],'p_number'=>$carInfo['number'],'type'=>6,'status'=>$carInfo['types']]);
    }

    public function getCarInfo($pid)
    {
        $data = Db::table('pk_carpos')->where('id', $pid)->find();
        return $data;
    }
}
