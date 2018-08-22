<?php

namespace app\admin\model;

use think\exception\PDOException;
use think\Model;

class Img extends Model
{
    protected $table = 'oyo_img';

    /**
     * 更新图片信息
     * @param $param
     * @return array
     */
    public function updateImg($param)
    {
        try {

            $result = $this->validate('ImgValidate')->save($param, ['id' => $param['id']]);

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }
            else {
                return ['code' => 1, 'data' => '', 'msg' => '更新成功'];
            }
        } catch (PDOException $e) {

            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 获取首页图片
     * @return array|false|\PDOStatement|string|Model
     */
    public function getPhoto($where)
    {
        return $this->where($where)->find();
    }
}