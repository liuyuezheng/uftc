<?php

namespace app\admin\model;

use think\Model;

class Stopcar extends Model
{
    protected $table = 'pk_stopcarrecord';
    public function getList($data){
        (isset($data['plate']) && !empty($data['plate'])) ? $where1['plate'] = ['like', '%' . $data['plate'] . '%']:$where1=[];
         if($data['mg_name']=='admin' && $data['manager']===''){
            $where4=[];
        }else if($data['mg_name']=='admin' && $data['manager']!==''){
            if(is_array($data['manager'])){
                $data['manager']=implode(',',$data['manager']);
                $where4['s.mg_id']=array('in',$data['manager']);
            }else{
                $where4['s.mg_id']=$data['manager'];
            }
            
        }else if($data['mg_name']!='admin'){
            $where4['s.mg_id']=$data['mg_id'];
        }

          $page = $this::alias('s')
        	->join('__USER__ u','s.uid=u.uid','left')
        	->field('s.*,u.username')
        	->order('id desc')
        	->where($where1)->where($where4)->paginate(25,false,['query'=>$data]);//dump($page);
            //$page->timelong=time_difference($page->timelong);
        //获得分页的页码列表信息 并 传递给模版：
        $pagelist = $page -> render();
        return ['page'=>$page,'pagelist'=>$pagelist,'data'=>$data];
    }
}
