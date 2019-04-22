<?php

namespace app\admin\model;

use think\Db;
use think\Model;

class Alluser extends Model
{
    protected $table="pk_user";
    public function getList($data,$data1){
    	 (isset($data['username']) && !empty($data['username'])) ? $where1['username'] = ['like', '%' . $data['username'] . '%']:$where1=[];
        (isset($data['phone']) && !empty($data['phone'])) ? $where2['phone'] = ['like', '%' . $data['phone'] . '%']:$where2=[];


//        (isset($data['phone']) && !empty($data['phone'])) ? $where2['phone'] = ['like', '%' . $data['phone'] . '%']:$where2=[];
        $page=$this::order('uid desc')->where($where1)
            ->where($where2)
            ->paginate(20,false,['query'=>$data]);
        $page2=$this::order('uid desc')->where($where1)
            ->where($where2)
            ->paginate(20,false,['query'=>$data])
        ->toArray();
//            ->each(function($item, $key){
//                $uid=$item['uid'];
                if(isset($data1['mg_id']) && !empty($data1['mg_id'])){
                    if($data1['mg_id']!=1 && $data1['mg_name']!='admin'){
                        $where3['mg_id']=['eq',$data1['mg_id']];
                    }else{
                        $where3=[];
                    }
                }else{
                    $where3=[];
                }
//                $cnumber=Db::table('pk_carpos')->where('uid',$uid)
//                    ->where('uuid',0)->where('uuuid',0)
//                    ->where('owner',2)
//                    ->where($where3)
//                    ->column('number');
//                $item['cnumber']=implode(',',$cnumber);
//            });

            foreach ($page2['data'] as $k=>$v){
                $cnumber=Db::table('pk_carpos')->where('uid',$v['uid'])
                    ->where('uuid',0)->where('uuuid',0)
                    ->where('owner',2)
                    ->where($where3)
                    ->column('number');
                $page2['data'][$k]['cnumber']=implode(',',$cnumber);
            }


        //获得分页的页码列表信息 并 传递给模版：
        $pagelist = $page -> render();
        return ['page'=>$page2,'pagelist'=>$pagelist,'data'=>$data];
    }
}
