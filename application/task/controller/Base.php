<?php

namespace app\task\controller;

use think\Controller;

/**
 * 模块公共控制类
 *
 * @category	Controller
 * @package	app\task\controller
 */
abstract class Base extends Controller
{

    use \traits\Log;
    /**
     * 任务ID
     * @static
     * @access protected
     * @var int
     */
    protected static $_nTaskId = 0;

    /**
     * 任务数据
     * @static
     * @access protected
     * @var array
     */
    protected static $_aTask = [];

    /**
     * 任务参数
     * @static
     * @access protected
     * @var array
     */
    protected static $_aParam = [];

    /**
     * 写入日志
     * @access protected
     * @param  array  $log     [in]日志数据
     * @param  string $sKeyword [in]关键字
     * @return void
     */
    protected function _writeLog($log, $sKeyword)
    {
        !$sKeyword;
        $log['task_id'] = self::$_nTaskId;
        $log['time'] = date('Y-m-d H:i:s', time());
        $logModel = new \app\common\model\TaskLog;
        $logModel->insert($log);
    }
}
