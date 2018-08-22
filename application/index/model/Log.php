<?php

namespace app\index\model;

use think\exception\PDOException;
use think\Model;

class Log extends Model
{

    /**
     * @var string 表名
     */
    protected $table = 'oyo_log';


    /**
     * 所有IP数据
     * @return false|static[]
     */
    public function getLists()
    {
        return $this->all();
    }

    /*添加数据*/
    public function addLog($param)
    {
        return $this->save($param);
    }

    /*个数*/
    public function getCount()
    {
        return $this->count();
    }

    /*获取访问IP列表*/
    public function getIpListByWhere($where, $offset, $limit)
    {
        return $this->where($where)->limit($offset, $limit)->order('last_time desc')->select();
    }

    /*获取访问IP总数*/
    public function getAllIp($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 删除访问ip记录
     * @param $id
     * @return array
     */
    public function delIp($id)
    {
        try {
            $this->where('id', $id)->delete();

            return ['code' => 1, 'data' => '', 'msg' => '删除访问记录成功'];

        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }


}