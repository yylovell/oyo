<?php

namespace app\task\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 任务主线控制类
 *
 * @category	Controller
 * @package	app\task\controller
 */
class Main extends Base
{
    protected $_tqModel;

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
        $this->_tqModel = new \app\common\model\TaskQueue();
    }

    /**
     * 主干线
     * @access public
     * @return int
     */
    public function index()
    {
        $this->plan();

        try
        {
            // 获取待执行的任务
            $aLocator = [
                'run_at' => ['elt', date('Y-m-d H:i:s', time())],
                'state' => ['eq', Sys::TASK_STATE_WAITTING],
            ];
            $aTasks = $this->_tqModel->where($aLocator)->limit(50)->field('id')->select();

            // 获取运行超时了的任务
            $aLocator = [
                'update_at' => ['elt', date('Y-m-d H:i:s', time() - Sys::SCIRPT_MAX_EXECUTE_TIME * 15)],
                'state' => ['eq', Sys::TASK_STATE_RUNNING],
            ];
            $aTimeOutTask = $this->_tqModel->where($aLocator)->limit(50)->field('id')->select();

            $aTasks = array_merge($aTasks->toArray(), $aTimeOutTask->toArray());
            foreach ($aTasks as $aTask)
            {
                $this->_exec("task/entrance/index/id/{$aTask['id']}");
            }

            return 0;
        }
        catch (Exception $ex)
        {
            $this->logError("主干线出错，错误信息：{$ex->getMessage()}");
        }
    }

    /**
     * 任务计划
     * @access public
     * @return void
     */
    public function plan()
    {
        try
        {
            $oTaskModel = new \app\common\model\Task();

            $sTime = date('Y-m-d H:i:s', time());

            $aLocator = [
                'is_send' => Sys::COMMON_YES,
            ];
            $aField = ['id', 'type', 'run_interval', 'run_time', 'plan_at', 'class_name', 'method_name', 'process_num'];
            $aTaskList = $oTaskModel->where($aLocator)->field($aField)->select();
            foreach ($aTaskList as $aTask)
            {
                // 进程数
                $nProcessNum = (int) $aTask['process_num'] ?: 1;

                switch ($aTask['type'])
                {
                    case Sys::TASK_PLAN_TYPE_INTERVAL:

                        if (time() < (int) $aTask['run_interval'] + strtotime($aTask['plan_at']))
                        {
                            break;
                        }

                        $oTaskModel->where('id', $aTask['id'])->update(['plan_at' => $sTime]);

                        for ($index = 0; $index < $nProcessNum; $index++)
                        {
                            $aInput = [
                                'task_id' => $aTask['id'],
                                'class_name' => $aTask['class_name'],
                                'method_name' => $aTask['method_name'],
                                //'run_at' => \Org\Util\Format::datetime(time() + $index * 5), // 延迟五秒
                                'param' => [
                                    'process_num' => $nProcessNum,
                                    'process_no' => $index,
                                ],
                            ];
                            $this->_tqModel->insertTask($aInput);
                        }

                        break;
                    case Sys::TASK_PLAN_TYPE_TIME:

                        $sRunTime = date('Y-m-d', time()) . ' ' . $aTask['run_time'];
                        if ($sRunTime <= $aTask['plan_at'] || time() < strtotime($sRunTime))
                        { // 已执行或未到时间
                            break;
                        }

                        $oTaskModel->where('id', $aTask['id'])->update(['plan_at' => $sTime]);

                        for ($index = 0; $index < $nProcessNum; $index++)
                        {
                            $aInput = [
                                'task_id' => $aTask['id'],
                                'class_name' => $aTask['class_name'],
                                'method_name' => $aTask['method_name'],
                                //'run_at' => \Org\Util\Format::datetime(time() + $index * 5), // 延迟五秒
                                'param' => [
                                    'process_no' => $index,
                                    'process_num' => $nProcessNum,
                                ],
                            ];
                            $this->_tqModel->insertTask($aInput);
                        }

                        break;
                    default:
                        break;
                }
            }
        }
        catch (Exception $ex)
        {
            $this->logError("任务计划出错，错误信息：{$ex->getMessage()}");
        }
    }

    /**
     * 执行命令
     * @access private
     * @param  string $sCommand [in]命令
     * @return void
     */
    private function _exec($sCommand)
    {
        static $_sCommand = null;

        if (is_null($_sCommand))
        {
            $strPhpPath = config('php_path');

            $_sCommand = "{$strPhpPath} {$_SERVER['SCRIPT_FILENAME']}";
            if (IS_WIN)
            {
                $_sCommand = "start /b {$_sCommand}";
            }
        }

        $sSecretKey = Sys::TASK_SECRET_KEY;

        $sCommand = "{$_sCommand} {$sCommand}/secret_key/{$sSecretKey}";

        IS_WIN or $sCommand .= ' > /dev/null 2>&1 & echo $!';

        if (-1 === pclose(popen($sCommand, 'r')))
        {
            throw new Exception('启动任务进程出错');
        }
    }
}
