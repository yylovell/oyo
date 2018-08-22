<?php

namespace app\task\controller;

use app\common\controller\Sms;
use think\Exception;

/**
 * 课程控制类
 *
 * @category	Controller
 * @package	app\task\controller
 */
class Learncourse extends Base
{

    /**
     * 初始化
     * @access protected
     * @return void
     */
    protected function _initialize()
    {
        // 设置运行限制时间
        set_time_limit(0);
        // 忽略用户中断
        ignore_user_abort(true);
    }

    /**
     * 每日检查是否有课程，并生成管理员日志
     * @access public
     * @return void
     */
    public function addLearnLog()
    {
        try
        {
            $vModel = new \app\common\model\Vipstudent();
            $aData = $vModel->getTodayData();

            if (empty($aData))
            {
                $log = '今日会员课: 暂无';
            }
            else
            {
                $log = '今日会员课：<br>';
                foreach ($aData as $k=>$item)
                {
                    $log .= $k . ' ,等级：';
                    foreach ($item as $v)
                    {
                        $log .= $v[0]['grade']['name'] . ' ,';
                    }
                    $log .= '<br>';
                }
            }

            //记录日志到管理员日志

            $aTraceList = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

            $strLine = $aTraceList[1]['line'];
            $strFile = $aTraceList[1]['file'];
            $strClass = $aTraceList[2]['class'];
            $strFunction = $aTraceList[2]['function'];

            $log = [
                'level' => 1,
                'pid' => getmypid(),
                'line' => $strLine,
                'ip' => '计划任务',
                'file' => $strFile,
                'method' => $strClass . '::' . $strFunction,
                'log' => $log,
            ];

            $log['admin_id'] = 1;
            $log['keyword'] = '';
            $log['time'] = date('Y-m-d H:i:s', time());
            $logModel = new \app\common\model\Operatelog;
            $logModel->insert($log);

        }
        catch (Exception $ex)
        {
            $this->logError("任务出错，错误信息：{$ex->getMessage()}");
        }
    }
}
