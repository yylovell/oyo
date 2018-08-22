<?php
namespace app\index\model;

use think\Model;

class News extends Model
{

    /**
     * @var string 表名
     */
    protected $table = 'oyo_news';


    public function getLists()
    {
        return $this->all();
    }


}