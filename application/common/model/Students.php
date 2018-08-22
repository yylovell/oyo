<?php

namespace app\common\model;

use app\common\controller\Sys;
use think\exception\PDOException;
use think\Model;

class Students extends Model
{

    protected $table = "oyo_students";

    /**
     * 根据搜索条件获列表信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getByWhere($where, $offset, $limit)
    {
        return $this->where($where)->limit($offset, $limit)->order('id')->select();
    }

    /**
     * 获取启用的学生
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getStudents()
    {
        return $this->where(['is_send' => Sys::NEWS_IS_SEND])->order('id')->select();
    }

    /**
     * 根据搜索条件获取所有学生的数量
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
    public function insertStudents($param)
    {
        try {
            $result = $this->validate('StudentsValidate')->save($param);
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
    public function editStudents($param)
    {
        try {
            $result = $this->validate('StudentsValidate')->save($param, ['id' => $param['id']]);

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
     * 启用、停用
     * @param $param
     * @return array
     */
    public function send($param)
    {
        try {
            if ($param['is_send'] == Sys::NEWS_IS_SEND)
            {
                $msg = '启用成功';
            }
            else
            {
                $msg = '已停用';
            }
            $result = $this->save($param, ['id' => $param['id']]);
            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }
            else {

                return ['code' => 1, 'data' => '', 'msg' => $msg];
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
     * 根据手机号获取信息
     * @param $phone
     * @return array|false|\PDOStatement|string|Model
     */
    public function getByPhone($phone)
    {
        return $this->where('phone', $phone)->find();
    }

    /**
     * 重置密码
     * @param $param
     * @return array
     */
    public function replacePassword($param)
    {
        try {
            $result = $this->save($param, ['id' => $param['id']]);
            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }
            else {

                return ['code' => 1, 'data' => '', 'msg' => '密码修改成功'];
            }

        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 修改头像
     * @param $param
     * @return array
     */
    public function updateAvatar($param)
    {
        try {
            $result = $this->save($param, ['id' => $param['id']]);
            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }
            else {

                return ['code' => 1, 'data' => '', 'msg' => '修改头像成功'];
            }

        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
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