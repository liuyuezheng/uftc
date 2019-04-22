<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/19
 * Time: 11:11
 */

namespace app\admin\controller;


use think\Controller;
use think\Db;

class MessageController extends Controller
{
    public function index(){
     return $this->fetch();
    }
    public function addMessage(){
       $content=input('post.content/s');
       $arr=array(
           // 'end_time'=> $list['endtime'],
           'title'=>'系统消息',
           'message'=>$content,
           'createtime'=>time(),
           'updatetime'=>time(),
           'type'=>6
       );
       $res=Db::table('pk_message')->insert($arr);;
       if($res){
           return true;
       }else{
           return false;
     }
    }
}