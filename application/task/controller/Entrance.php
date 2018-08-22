<?php

namespace app\task\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 任务运行入口类
 *
 * @category	Controller
 * @package	app\task\controller
 */
class Entrance extends Base
{
    protected $_tModel;

    /**
     * 初始化
     * @access protected
     * @return void
     */
    protected function _initialize()
    {
        // 设置运行限制时间
        set_time_limit(Sys::SCIRPT_MAX_EXECUTE_TIME);
        // 忽略用户中断
        ignore_user_abort(true);
        $this->_tModel = new \app\common\model\Task();
    }

    /**
     * 任务唯一入口
     * @return bool
     */
    public function index()
    {
        $bStart = false;
        $oQueueModel = null;
        $aTask = [];

        try
        {
            self::$_nTaskId = input('id');
            if (!self::$_nTaskId)
            {
                throw new Exception('任务ID非法');
            }

            $oQueueModel = new \app\common\model\TaskQueue();

            if (input('secret_key') != Sys::TASK_SECRET_KEY)
            {
                throw new Exception('任务密钥错误');
            }

            self::$_aTask = $aTask = $oQueueModel->getById(self::$_nTaskId);
            if ($aTask['state'] == Sys::TASK_STATE_FINISH)
            {
                return;
            }
            elseif ($aTask['run_count'] > Sys::TASK_MAX_TRY_COUNT)
            {
                $aData = ['state' => Sys::TASK_STATE_FAIL];
                $oQueueModel->where('id', self::$_nTaskId)->update($aData);
                return;
            }
            self::$_aParam = json_decode($aTask['param'], true);

            $sClass = $aTask['class_name'];
            if (!class_exists("app\\task\\controller\\$sClass"))
            {
                throw new Exception('任务执行类不存在');
            }
            if (!method_exists("app\\task\\controller\\$sClass", $aTask['method_name']))
            {
                throw new Exception('任务执行类方法不存在');
            }

            if ($aTask['task_id'])
            {
                $aData = [
                    'run_count' => ['exp', 'run_count+1'],
                    'run_at' => date('Y-m-d H:i:s', time()),
                ];
                $this->_tModel->where('id', $aTask['task_id'])->update($aData);
            }

            $aData = [
                'state' => Sys::TASK_STATE_RUNNING,
                'pid' => getmypid(),
                'run_count' => ['exp', 'run_count+1'],
                'start_at' => date('Y-m-d H:i:s', time()),
            ];
            $oQueueModel->where('id', self::$_nTaskId)->update($aData);

            $bStart = true;

            $sMethod = $aTask['method_name'];

            action("$sClass/$sMethod");

            $bStart = false;

            $aData = ['state' => Sys::TASK_STATE_FINISH];
            $oQueueModel->where('id', self::$_nTaskId)->update($aData);
        }
        catch (Exception $ex)
        {
            $this->logError("执行任务出错，错误信息：{$ex->getMessage()}");

            if ($bStart)
            {
                if ($aTask['run_count'] + 1 >= Sys::TASK_MAX_TRY_COUNT)
                {
                    $aData = ['state' => Sys::TASK_STATE_FAIL];
                }
                else
                {
                    $aData = [
                        'state' => Sys::TASK_STATE_WAITTING,
                    ];
                }
                $oQueueModel->where('id', self::$_nTaskId)->update($aData);

                if ($aTask['task_id'])
                {
                    $aData = [
                        'fail_count' => ['exp', 'fail_count+1'],
                    ];
                    $this->_tModel->where('id', $aTask['task_id'])->update($aData);
                }
            }

            throw $ex;
        }
    }
}
