<?php

namespace traits;


/**
 * 控制器日志Trait
 *
 * @category	trait
 * @package	system\traits
 */
trait Log
{


    /**
     * 调试日志
     * @access protected
     * @param  	string $log	    [in]日志信息
     * @param  	string $keyword [in opt]关键字
     * @return void
     */
    protected function logDebug($log, $keyword = '')
    {
        $this->_log($log, 5, $keyword);
    }

    /**
     * 通知日志
     * @access protected
     * @param  	string $log	    [in]日志信息
     * @param  	string $keyword [in opt]关键字
     * @return void
     */
    protected function logNotice($log, $keyword = '')
    {
        $this->_log($log, 3, $keyword);
    }

    /**
     * 错误日志
     * @access protected
     * @param  string $log     [in]日志信息
     * @param  string $keyword [in opt]关键字
     * @return void
     */
    protected function logError($log, $keyword = '')
    {
        $this->_log($log, 2, $keyword);
    }

    /**
     * 信息日志
     * @access protected
     * @param  	string $log     [in]日志信息
     * @param  	string $keyword [in opt]关键字
     * @return void
     */
    protected function logInfo($log, $keyword = '')
    {
        $this->_log($log, 1, $keyword);
    }

    /**
     * 警告日志
     * @access protected
     * @param  	string $log     [in]日志信息
     * @param  	string $keyword [in opt]关键字
     * @return void
     */
    protected function logWarn($log, $keyword = '')
    {
        $this->_log($log, 4, $keyword);
    }

    /**
     * 写日志
     * @access protected
     * @param  string $log     [in]日志信息
     * @param  int    $level   [in]日志等级
     * @param  string $keyword [in opt]关键字
     * @return void
     */
    protected function _log($log, $level, $keyword = '')
    {
        // 判断记录的级别
        $recordLevel = [1, 2, 3, 4, 5];

        if (!in_array($level, $recordLevel))
        {
            return;
        }

        $aTraceList = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

        $strLine = $aTraceList[1]['line'];
        $strFile = $aTraceList[1]['file'];
        $strClass = $aTraceList[2]['class'];
        $strFunction = $aTraceList[2]['function'];

        $log = [
            'level' => $level,
            'pid' => getmypid(),
            'line' => $strLine,
            'ip' => get_client_ip(),
            'file' => $strFile,
            'method' => $strClass . '::' . $strFunction,
            'log' => $log,
        ];

        $this->_writeLog($log, $keyword);
    }

    /**
     * 写入日志
     * @access protected
     * @param  array  $log     [in]日志数据
     * @param  string $keyword [in]关键字
     * @return void
     */
    protected function _writeLog($log, $keyword)
    {
        $log || $keyword;
        return;
    }
}
