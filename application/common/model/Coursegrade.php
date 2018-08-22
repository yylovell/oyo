<?php
// +----------------------------------------------------------------------
// | snake
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\common\model;

use app\common\controller\Sys;
use think\exception\PDOException;
use think\Model;

class Coursegrade extends Model
{
    // 确定链接表名
    protected $table = 'oyo_course_grade';

    /**
     * 获取数据
     * @return mixed
     */
    public function getList()
    {
        return $this->field('id,name,des,is_send,type_id pid')->select();
    }

    /**
     * 获取启用的数据
     * @return mixed
     */
    public function getSendList()
    {
        return $this->where(['is_send' => Sys::NEWS_IS_SEND])->field('id,name,des,is_send,type_id pid')->select();
    }

    /**
     * 插入信息
     * @param $param
     * @return array
     */
    public function insert($param)
    {
        try{

            $this->save($param);
            return msg(1, $this->id, '添加成功');
        }catch(PDOException $e){

            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 编辑
     * @param $param
     * @return array
     */
    public function edit($param)
    {
        try{

            $this->save($param, ['id' => $param['id']]);
            return msg(1, '', '编辑成功');
        }catch(PDOException $e){

            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 删除
     * @param $id
     * @return array
     */
    public function del($id)
    {
        try{

            $this->where('id', $id)->delete();
            return msg(1, '', '删除成功');

        }catch(PDOException $e){
            return msg(-1, '', $e->getMessage());
        }
    }
}