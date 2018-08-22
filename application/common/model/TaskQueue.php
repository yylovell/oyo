<?php

namespace app\common\model;

use app\common\controller\Sys;
use think\exception\PDOException;
use think\Model;

class TaskQueue extends Model
{

    protected $table = "oyo_task_queue";

    public function task()
    {
        return $this->belongsTo('Task', 'task_id', 'id');
    }

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
        return $this->with(['task'])->where($where)->order($order)->limit($offset, $limit)->select();
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
     * 插入任务
     * @access public
     * @param  array $aTask [in]任务数据，格式：
     * <pre><code>
     * array(
     * 	'class_name' => '类名',
     * 	'method_name' => '方法名',
     * 	'[param]' => array('参数'),
     * 	'[p_order]' => '优先级',
     * 	'[run_at]' => '运行时间',
     *  '[plan_id]' => '计划ID',
     * );
     * </code></pre>
     * @return int 任务ID
     */
    public function insertTask(array $aTask)
    {

        $strCurTime = date('Y-m-d H:i:s', time());

        isset($aTask['p_order']) || $aTask['p_order'] = 0;
        isset($aTask['run_at']) || $aTask['run_at'] = $strCurTime;
        isset($aTask['plan_id']) || $aTask['plan_id'] = 0;
        $aTask['param']['_secret_key'] = Sys::TASK_SECRET_KEY;

        $aData = [
            'task_id' => $aTask['task_id'],
            'p_order' => $aTask['p_order'],
            'create_at' => $strCurTime,
            'update_at' => $strCurTime,
            'run_at' => $aTask['run_at'],
            'run_count' => 0,
            'state' => Sys::TASK_STATE_WAITTING,
            'class_name' => $aTask['class_name'],
            'method_name' => $aTask['method_name'],
            'param' => json_encode($aTask['param'], JSON_UNESCAPED_UNICODE),
        ];

        $this->insert($aData);
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
     * 编辑信息
     * @param $param
     * @return array
     */
    public function edit($param)
    {
        try {
            $result = $this->save($param, ['id' => $param['id']]);

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