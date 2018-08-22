<?php

namespace app\common\model;

use think\exception\PDOException;
use think\Model;

class TaskLog extends Model
{

    protected $table = "oyo_task_log";

    /**
     * 根据搜索条件获列表信息
     * @param $where
     * @param $order
     * @param $offset
     * @param $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getByWhere($where, $order, $offset, $limit)
    {
        return $this->where($where)->order($order)->limit($offset, $limit)->select();
    }

    /**
     * 根据搜索条件获取所有的数量
     * @param $where
     * @return int
     */
    public function getAll($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 插入信息
     * @param $param
     * @return array
     */
    public function insert($param)
    {
        try {
            $result = $this->save($param);

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }
            else {
                return ['code' => 1, 'data' => $this->id, 'msg' => '添加成功'];
            }
        } catch (PDOException $e) {

            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 根据id获取信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getById($id)
    {
        return $this->where('id', $id)->find();
    }

}