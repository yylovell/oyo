<?php
/**
 * Created by PhpStorm.
 * User: yaoyuan
 * Date: 2017/6/6
 * Time: 下午2:20
 */
namespace app\index\model;

use think\Model;

class Cases extends Model
{

    /**
     * @var string 表名
     */
    protected $table = 'oyo_cases';


    public function getLists()
    {
        return $this->all();
    }


}