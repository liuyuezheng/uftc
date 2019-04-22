<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Permission extends Model
{
    //要去使用SoftDelete中的成员
    use SoftDelete;


    //设置软删除对应的数据表"删除字段"
    protected $deleteTime = 'delete_time';

    public static function invoke(){
        $id=request()->param('ps_id');
        return self::get($id);
    }
}
