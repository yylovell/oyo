<?php

namespace app\admin\model;

use think\exception\PDOException;
use think\Model;

class Slider extends Model
{
    protected $table = 'oyo_slider';

    /**
     * 根据搜索条件获取列表信息
     * @param $where
     * @param $offset
     * @param $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getSliderByWhere($where, $offset, $limit)
    {
        return $this->where($where)->limit($offset, $limit)->order(['weight_val' => 'asc', 'update_time' => 'desc'])->select();
    }

    /**
     * 根据搜索条件获取所有的轮播图数量
     * @param $where
     * @return int
     */
    public function getAllSlider($where)
    {
        return $this->where($where)->count();
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
     * 插入轮播图信息
     * @param $param
     * @return array
     */
    public function insertSlider($param)
    {
        try {
            $result = $this->validate('SliderValidate')->save($param);

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }
            else {
                return ['code' => 1, 'data' => $this->id, 'msg' => '添加轮播图成功'];
            }
        } catch (PDOException $e) {

            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑轮播图信息
     * @param $param
     * @return array
     */
    public function editSlider($param)
    {
        try {
            $result = $this->validate('SliderValidate')->save($param, ['id' => $param['id']]);

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }
            else {

                return ['code' => 1, 'data' => '', 'msg' => '编辑轮播图成功'];
            }
        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 根据轮播图id获取信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getSliderById($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * 删除轮播图
     * @param $id
     * @return array
     */
    public function delSlider($id)
    {
        try {
            $this->where('id', $id)->delete();

            return ['code' => 1, 'data' => '', 'msg' => '删除轮播图成功'];

        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 根据ID找到图片
     */
    public function getPhotoById($id)
    {
        return $this->where('id', $id)->field('photo')->find();
    }
}