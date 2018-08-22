<?php
namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 定时任务控制类
 *
 * Class Benefit
 * @package app\admin\controller
 */
class Task extends Base
{

    public $_tModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_tModel = new \app\common\model\Task();
    }

    /**
     * 列表
     * @return mixed
     */
    public function lists()
    {

        if (request()->isAjax()) {

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['name'] = ['like', '%' . $param['searchText'] . '%'];
            }

            $selectResult = $this->_tModel->getByWhere($where, $offset, $limit);

            //生成按钮
            foreach ($selectResult as $key => $vo) {
                if ($selectResult[$key]['type'] == Sys::TASK_PLAN_TYPE_INTERVAL)
                {
                    $selectResult[$key]['time'] = $vo['run_interval'];
                }
                elseif ($selectResult[$key]['type'] == Sys::TASK_PLAN_TYPE_TIME)
                {
                    $selectResult[$key]['time'] = $vo['run_time'];
                }

                $selectResult[$key]['type'] = Sys::getTaskTypeName($vo['type']);
                $selectResult[$key]['is_send'] = Sys::getCommonIS($vo['is_send']);
                $selectResult[$key]['count'] = $vo['run_count'] . ' [失败' . $vo['fail_count']. ']';
                $operate = [
                    '编辑' => [
                        'href' => url('edit', ['id' => $vo['id']]),
                        'auth' => 'task/edit'
                    ],
                    '队列' => [
                        'href' => url('queue', ['task_id' => $vo['id']]),
                        'auth' => 'task/queue'
                    ],
                    '删除' => [
                        'href' => "javascript:del('" . $vo['id'] . "')",
                        'auth' => 'task/del'
                    ]
                ];

                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_tModel->getAll($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }
        return $this->fetch();
    }

    //添加
    public function add()
    {
        //
        try {
            if (request()->isPost()) {

                $param = input();
                $param['process_num'] = abs((int) $param['process_num']) ?: 1;
                $arr = ['name' => $param['name'], 'process_num' => $param['process_num'], 'class_name' => $param['class_name'], 'method_name' => $param['method_name'], 'type' => $param['type'], 'run_interval' => $param['run_interval'], 'run_time' => $param['run_time'], 'is_send' => $param['is_send']

                ];
                $arr['create_at'] = $arr['update_at'] = date('Y-m-d H:i:s', time());

                $flag = $this->_tModel->insert($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
            $this->assign('type_map', Sys::getTaskTypeName());

            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }

    }

    //编辑
    public function edit()
    {
        try{
            if (request()->isPost()) {
                $param = input();

                $param['process_num'] = abs((int) $param['process_num']) ?: 1;
                $arr = ['id' => $param['id'], 'name' => $param['name'], 'process_num' => $param['process_num'], 'class_name' => $param['class_name'], 'method_name' => $param['method_name'], 'type' => $param['type'], 'run_interval' => $param['run_interval'], 'run_time' => $param['run_time'], 'is_send' => $param['is_send']

                ];
                $arr['update_at'] = date('Y-m-d H:i:s', time());
                $flag = $this->_tModel->edit($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $id = input('param.id');
            $aData = $this->_tModel->getById($id);

            $this->assign('data', $aData);
            $this->assign('type_map', Sys::getTaskTypeName());

            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    //删除
    public function del()
    {
        $id = input('param.id');

        $flag = $this->_tModel->del($id);

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    // 队列
    public function queue()
    {
        $task_id = input('task_id');

        if (request()->isAjax()) {

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $order = 'start_at desc';

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['task_id'] = $param['searchText'];
            }
            if (isset($param['state']) && !empty($param['state']) && (int)$param['state'] !== 1000) {
                $where['state'] = $param['state'];
            }

            $_tqModel = new \app\common\model\TaskQueue();
            $selectResult = $_tqModel->getByWhere($where, $order, $offset, $limit);

            //生成按钮
            foreach ($selectResult as $key => $vo) {

                $selectResult[$key]['state'] = Sys::getQueueStateName($selectResult[$key]['state']);
                $operate = [
                    '日志' => [
                        'href' => url('log', ['queue_id' => $vo['id']]),
                        'auth' => 'task/log'
                    ]
                ];

                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $_tqModel->getAll($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        $this->assign(['task_id' => $task_id, 'state_map' => Sys::getQueueStateName()]);
        return $this->fetch();
    }

    // 日志
    public function log()
    {
        $queue_id = input('queue_id');

        if (request()->isAjax()) {

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $order = 'time desc';

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['queue_id'] = $param['searchText'];
            }

            $_tlModel = new \app\common\model\TaskLog();
            $selectResult = $_tlModel->getByWhere($where, $order, $offset, $limit);

            //生成按钮
            foreach ($selectResult as $key => $vo) {
                $selectResult[$key]['level'] = color(Sys::getLogLevelName($selectResult[$key]['level']), $selectResult[$key]['level'], 'width:30px;height:30px;border-radius:50%;text-align:center;line-height:30px;padding:0;margin:0;');
            }

            $return['total'] = $_tlModel->getAll($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }
        $this->assign('queue_id', $queue_id);
        return $this->fetch();
    }
}