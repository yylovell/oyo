<?php

namespace app\admin\model;

use think\exception\PDOException;
use think\Model;

class Join extends Model
{
    protected $table = 'oyo_join';

    /**
     * 根据搜索条件获职位列表信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getJoinByWhere($where, $offset, $limit)
    {
        return $this->where($where)->limit($offset, $limit)->order('update_time desc')->select();
    }

    /**
     * 根据搜索条件获取所有的职位数量
     * @param $where
     * @return int
     */
    public function getAllJoin($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 插入职位信息
     * @param $param
     * @return array
     */
    public function insertJoin($param)
    {
        try {
            $result = $this->validate('JoinValidate')->save($param);

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }
            else {
                return ['code' => 1, 'data' => $this->id, 'msg' => '添加职位成功'];
            }
        } catch (PDOException $e) {

            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑职位信息
     * @param $param
     * @return array
     */
    public function editJoin($param)
    {
        try {

            $result = $this->validate('JoinValidate')->save($param, ['id' => $param['id']]);

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }
            else {

                return ['code' => 1, 'data' => '', 'msg' => '编辑职位成功'];
            }
        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 根据职位id获取信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getJoinById($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * 删除职位
     * @param $id
     * @return array
     */
    public function delJoin($id)
    {
        try {
            $this->where('id', $id)->delete();

            return ['code' => 1, 'data' => '', 'msg' => '删除职位成功'];

        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}