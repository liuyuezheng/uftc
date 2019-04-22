<?php

namespace app\api\controller;
use think\Controller;
use think\Request;
use think\Db;

class PollController extends Controller{

    private $param;
    function __construct(Request $request = null)
    {
        parent::__construct();
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with, content-type');

        $this->param = $request->param();
    }

    public function pollPro(){
        if ($this->checkName()) {

            $name = $this->param['name'];
            $awardName = $this->param['awardName'];
            $proName = $this->param['proName'];
            $isPoll = Db::table('pk_record')->where(['name'=>$name,'awardName'=>$awardName])->find();
            if (!empty($isPoll)) {
                return success(1001,'','不能重复投票');
            }

            $where['proName'] = ['eq', $proName];
            $where['awardName'] = ['eq', $awardName];
            Db::table('pk_count')->where($where)->setInc('count');
            $time = date('Y-m-d H:i:s', time());
            Db::table('pk_record')->insert(['name'=>$name, 'proName'=>$proName, 'awardName'=>$awardName, 'create_time'=>$time]);
            return success(1000,'','投票成功');
        }
        return success(1002,'','姓名有误');
    }

    public function reList(){
        $result = Db::table('pk_record')->select();
        $this->assign('list',$result);
        $chuangyiNum = Db::table('pk_count')->where('awardName', '最佳创意奖')->max('count');
        $chuangyi = Db::table('pk_count')->where(['awardName'=>'最佳创意奖', 'count'=>$chuangyiNum])->value('proName');
        $biaoyanNum = Db::table('pk_count')->where('awardName', '最佳表演奖')->max('count');
        $biaoyan = Db::table('pk_count')->where(['awardName'=>'最佳表演奖', 'count'=>$biaoyanNum])->value('proName');
        $renqiNum = Db::table('pk_count')->where('awardName', '最佳人气奖')->max('count');
        $renqi = Db::table('pk_count')->where(['awardName'=>'最佳人气奖', 'count'=>$renqiNum])->value('proName');
        $this->assign('chuangyiNum',$chuangyiNum);
        $this->assign('chuangyi',$chuangyi);
        $this->assign('biaoyanNum',$biaoyanNum);
        $this->assign('biaoyan',$biaoyan);
        $this->assign('renqiNum',$renqiNum);
        $this->assign('renqi',$renqi);
        return $this -> fetch('poll/relist');
    }


    public function indexs(){
        return $this -> fetch('poll/ingdex');
    }

    public function page(){
        return $this -> fetch('poll/page');
    }

    private function checkName(){
        $name = $this->param['name'];
        $nameArr = Db::table('pk_year')->column('name');
        return in_array($name, $nameArr);
    }

    public function intt(){
        $arr1 = ['最佳创意奖','最佳表演奖','最佳人气奖'];
        $arr2 = ['舞蹈串烧','年三十','小手拉大手','雷人舞','夏洛特的烦恼'];
        Db::table('pk_record')->where('id','>',0)->delete();
        foreach ($arr1 as $k=>$v){
            foreach ($arr2 as $k1=>$v1){
                Db::table('pk_count')->where('count','neq',0)->update(['count'=>0]);
            }
        }
        echo 'ok';
    }
}