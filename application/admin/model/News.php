<?php

namespace app\admin\model;

use app\common\controller\Sys;
use think\exception\PDOException;
use think\Model;

class News extends Model
{
    protected $table = 'oyo_news';

    /**
     * 根据搜索条件获案例列表信息
     * @param $where
     * @param $offset
     * @param $limit
     * @param $order
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getNewsByWhere($where, $offset, $limit, $order)
    {
        return $this->where($where)->limit($offset, $limit)->order($order)->select();
    }

    /**
     * 根据搜索条件获取所有的新闻数量
     * @param $where
     * @return int
     */
    public function getAllNews($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 插入新闻信息
     * @param $param
     * @return array
     */
    public function insertNews($param)
    {
        try {
            $result = $this->validate('NewsValidate')->save($param);

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }
            else {
                return ['code' => 1, 'data' => $this->id, 'msg' => '添加新闻成功'];
            }
        } catch (PDOException $e) {

            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 判断推送的新闻个数
     * @param int $id
     * @return int
     * @internal param $type
     */
    public function sendCount($id = 0)
    {
        if(!$id)
        {
            return $this->where(['is_send' => Sys::NEWS_IS_SEND])->count();
        }
        return $this->where(['is_send' => Sys::NEWS_IS_SEND, 'id' => ['neq', $id]])->count();

    }

    /**
     * 获取字段最大值
     * @param $feild
     * @return int
     */
    public function getMax($feild)
    {
        return $this->max($feild);
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
     * 根据ID找到图片
     */
    public function getPhotoById($id)
    {
        return $this->where('id', $id)->field('photo')->find();
    }

    /**
     * 编辑新闻信息
     * @param $param
     * @return array
     */
    public function editNews($param)
    {
        try {

            $result = $this->validate('NewsValidate')->save($param, ['id' => $param['id']]);

            if (false === $result) {
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }
            else {

                return ['code' => 1, 'data' => '', 'msg' => '编辑新闻成功'];
            }
        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 根据新闻id获取信息
     * @param $id
     * @return array|false|\PDOStatement|string|Model
     */
    public function getNewsById($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * 删除新闻
     * @param $id
     * @return array
     */
    public function delNews($id)
    {
        try {
            $this->where('id', $id)->delete();

            return ['code' => 1, 'data' => '', 'msg' => '删除新闻成功'];

        } catch (PDOException $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 首页显示、不显示
     * @param $param
     * @return array
     */
    public function send($param)
    {
        try {
            if ($param['is_send'] == Sys::NEWS_IS_SEND)
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