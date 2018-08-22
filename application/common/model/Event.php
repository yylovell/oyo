<?php

namespace app\common\model;

use think\exception\PDOException;
use think\Model;

class Event extends Model
{

    /**
     * @var string 表名
     */
    protected $table = 'oyo_event';

    /**
     * 根据搜索条件获列表信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getByWhere($where, $offset, $limit)
    {
        return $this->where($where)->limit($offset, $limit)->order(['time' => 'desc', 'id' => 'desc'])->select();
    }

    /**
     * 根据时间降序查找信息
     * @return mixed
     */
    public function getEvents()
    {
        return $this->order(['time', 'id'])->select();
    }

    /**
     * 根据搜索条件获取所有的职位数量
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
    public function insertEvent($param)
    {
        try {
            $result = $this->validate('EventValidate')->save($param);

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
     * 编辑信息
     * @param $param
     * @return array
     */
    public function editEvent($param)
    {
        try {

            $result = $this->validate('EventValidate')->save($param, ['id' => $param['id']]);

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }
            else {

                return ['code' => 1, 'data' => '', 'msg' => '编辑成功'];
            }
        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
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

    /**
     * 删除
     * @param $id
     * @return array
     */
    public function del($id)
    {
        try {
            $this->where('id', $id)->delete();

            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];

        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }


}