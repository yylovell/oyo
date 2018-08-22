<?php

namespace app\admin\model;

use app\common\controller\Sys;
use think\exception\PDOException;
use think\Model;

class Cases extends Model
{
    protected $table = 'oyo_cases';

    public function category()
    {
        return $this->belongsTo('app\common\model\CaseCat', 'type', 'id');
    }

    /**
     * 根据搜索条件获案例列表信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getCasesByWhere($where, $offset, $limit)
    {
        return $this->with(['category'])->where($where)->limit($offset, $limit)->order('create_time desc')->select();
    }

    /**
     * 根据搜索条件获取所有的案例数量
     * @param $where
     * @return int
     */
    public function getAllCases($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 插入案例信息
     * @param $param
     * @return array
     */
    public function insertCases($param)
    {
        try {
            if ($param['photo1']) {
                $result = $this->validate('CasesValidate')->save($param);
            }else{
                $result = $this->save($param);
            }


            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }
            else {
                return ['code' => 1, 'data' => $this->id, 'msg' => '添加案例成功'];
            }
        } catch (PDOException $e) {

            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 判断该类型下的经典案例个数
     * @param $type
     * @return int
     */
    public function classicalCount($type, $id = 0)
    {
        if(!$id)
        {
            return $this->where(['is_classical' => Sys::CASES_IS_CLASSICAL, 'type' => $type])->count();
        }
        return $this->where(['is_classical' => Sys::CASES_IS_CLASSICAL, 'type' => $type, 'id' => ['neq', $id]])->count();
    }

    /**
     * 返回照片是否存在
     * @param string $path 图片路径
     * @return int|string 1、0
     */
    public function getPhotoPath($path)
    {
        return $this->where(['photo' => $path])->count();
    }
    /**
     * 返回详情照片是否存在
     * @param string $where 条件
     * @return int|string 1、0
     */
    public function getDetailPhotoPath($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 根据ID找到图片
     */
    public function getPhotoById($id)
    {
        return $this->where('id', $id)->field('photo')->find();
    }

    /**
     * 编辑案例信息
     * @param $param
     * @return array
     */
    public function editCases($param)
    {
        try {
            if ($param['photo1']) {
                $result = $this->validate('CasesValidate')->save($param, ['id' => $param['id']]);
            }else{
                $result = $this->save($param, ['id' => $param['id']]);
            }

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }
            else {

                return ['code' => 1, 'data' => '', 'msg' => '编辑案例成功'];
            }
        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 根据案例id获取信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getCasesById($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * 删除案例
     * @param $id
     * @return array
     */
    public function delCases($id)
    {
        try {
            $this->where('id', $id)->delete();

            return ['code' => 1, 'data' => '', 'msg' => '删除案例成功'];

        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 首页显示、不显示
     * @param $param
     * @return array
     */
    public function classical($param)
    {
        try {
            if ($param['is_classical'] == Sys::CASES_IS_CLASSICAL)
            {
                $msg = '首页显示';
            }
            else
            {
                $msg = '首页不显示';
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
}