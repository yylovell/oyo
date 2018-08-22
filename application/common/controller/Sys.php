<?php
namespace app\common\controller;

class Sys
{

    /*经典种类*/
    const CASES_NOT_CLASSICAL = 0;
    const CASES_IS_CLASSICAL = 1;

    /*推送种类*/
    const NEWS_NOT_SEND = 0;
    const NEWS_IS_SEND = 1;

    /*公共是否*/
    const COMMON_NOT = 0;
    const COMMON_YES = 1;

    /*轮播图类型*/
    const SLIDER_TYPE_CASE_DETAIL = 0;
    const SLIDER_TYPE_CASE_LISTS = 1;
    const SLIDER_TYPE_CASE_LETTER = 5;
    const SLIDER_TYPE_NEWS_DETAIL = 2;
    const SLIDER_TYPE_NEWS_LISTS = 3;
    const SLIDER_TYPE_JOIN = 4;
    const SLIDER_TYPE_CASE_LETTER_B = 6;


    /*是否为菜单项*/
    const IS_MENU_NO = 1;
    const IS_MENU_YES = 2;

    /*图片类型*/
    const IMG_IS_NEWS = 0;
    const IMG_IS_PARTNER = 1;
    const IMG_IS_PUBLIC = 2;

    /*设备类型*/
    const IS_PC = 0;
    const IS_MOBILE = 1;

    /*课程类型*/
    const COURSE_TYPE_LEARN = 0;
    const COURSE_TYPE_VIP = 1;

    /*学员类型*/
    const STUDENT_TYPE_ADULT = 0;
    const STUDENT_TYPE_CHILDREN = 1;

    /*体验课时间类型*/
    const LEARN_TIME_TYPE_MON = 1;
    const LEARN_TIME_TYPE_TUE = 2;
    const LEARN_TIME_TYPE_WED = 3;
    const LEARN_TIME_TYPE_THU = 4;
    const LEARN_TIME_TYPE_FRI = 5;
    const LEARN_TIME_TYPE_SAT = 6;
    const LEARN_TIME_TYPE_SUN = 0;

    /* 日志等级 */
    /** 信息 */
    const LOG_LEVEL_INFO = 1; //
    /** 错误 */
    const LOG_LEVEL_ERROR = 2; //
    /** 通知 */
    const LOG_LEVEL_NOTICE = 3; //
    /** 警告 */
    const LOG_LEVEL_WARN = 4; //
    /** 调试 */
    const LOG_LEVEL_DEBUG = 5;

    /* 计划任务类型 */
    /** 间隔运行 */
    const TASK_PLAN_TYPE_INTERVAL = 1; //
    /** 定时运行 */
    const TASK_PLAN_TYPE_TIME = 2; //

    /* 计划任务队列状态 */
    /** 待执行 */
    const TASK_STATE_WAITTING = 1; //
    /** 执行中 */
    const TASK_STATE_RUNNING = 2; //
    /** 执行成功 */
    const TASK_STATE_FINISH = 3; //
    /** 执行出错 */
    const TASK_STATE_FAIL = 4; //

    /** 任务密钥 */
    const TASK_SECRET_KEY = 'M2C0RsRxGujg74Qt0sgWRU1jnStATixIYv5T';

    /** 脚本最大执行时间 */
    const SCIRPT_MAX_EXECUTE_TIME = 120;

    /** 任务最多重试次数 */
    const TASK_MAX_TRY_COUNT = 3;

    /**
     * 日志等级显示映射
     * @static
     * @access private
     * @var array
     */
    private static $_aLogLevelMaps = [
        self::LOG_LEVEL_INFO => '信息',
        self::LOG_LEVEL_ERROR => '错误',
        self::LOG_LEVEL_NOTICE => '通知',
        self::LOG_LEVEL_WARN => '警告',
        self::LOG_LEVEL_DEBUG => '调试',
    ];

    /**
     * 体验课时间类型映射
     * @static
     * @access private
     * @var array
     */
    private static $_aLearnTimeMap = [
        self::LEARN_TIME_TYPE_MON => '周一',
        self::LEARN_TIME_TYPE_TUE => '周二',
        self::LEARN_TIME_TYPE_WED => '周三',
        self::LEARN_TIME_TYPE_THU => '周四',
        self::LEARN_TIME_TYPE_FRI => '周五',
        self::LEARN_TIME_TYPE_SAT => '周六',
        self::LEARN_TIME_TYPE_SUN => '周日',
    ];

    /**
     * 学员类型映射
     * @static
     * @access private
     * @var array
     */
    private static $_aStudentTypeMap = [
        self::STUDENT_TYPE_ADULT => '成人',
        self::STUDENT_TYPE_CHILDREN => '儿童',
    ];

    /**
     * 课程类型映射
     * @static
     * @access private
     * @var array
     */
    private static $_aCourseTypeMap = [
        self::COURSE_TYPE_LEARN => '体验课',
        self::COURSE_TYPE_VIP => '会员课',
    ];

    /**
     * 计划任务类型映射
     * @static
     * @access private
     * @var array
     */
    private static $_aTaskTypeMap = [
        self::TASK_PLAN_TYPE_INTERVAL => '间隔运行',
        self::TASK_PLAN_TYPE_TIME => '定时运行',
    ];

    /**
     * 计划任务队列状态映射
     * @static
     * @access private
     * @var array
     */
    private static $_aQueueMaps = [
        self::TASK_STATE_WAITTING => '待执行',
        self::TASK_STATE_RUNNING => '执行中',
        self::TASK_STATE_FINISH => '执行成功',
        self::TASK_STATE_FAIL => '执行出错',
    ];

    /**
     * 是否为菜单项映射
     * @static
     * @access private
     * @var array
     */
    private static $_aIsMenuMap = [self::IS_MENU_YES => '是', self::IS_MENU_NO => '否',];

    /**
     * 经典映射
     * @static
     * @access private
     * @var array
     */
    private static $_aCasesClassicalMap = [self::CASES_IS_CLASSICAL => '是', self::CASES_NOT_CLASSICAL => '否',];

    /**
     * 推送映射
     * @static
     * @access private
     * @var array
     */
    private static $_aNewsSendMap = [self::NEWS_IS_SEND => '是', self::NEWS_NOT_SEND => '否',];

    /**
     * 置顶映射
     * @static
     * @access private
     * @var array
     */
    private static $_aNewsIsTop = [self::COMMON_YES => '已置顶', self::COMMON_NOT => '未置顶',];

    /**
     * 是否映射
     * @static
     * @access private
     * @var array
     */
    private static $_aCommonMap = [self::COMMON_YES => '是', self::COMMON_NOT => '否',];

    /**
     * 是否已读映射
     * @static
     * @access private
     * @var array
     */
    private static $_aLogIsRead = [self::COMMON_YES => '已读', self::COMMON_NOT => '未读',];

    /**
     * 轮播图类型映射
     * @static
     * @access private
     * @var array
     */
    private static $_aSliderMap = [
        self::SLIDER_TYPE_CASE_DETAIL => '案例详情',
        self::SLIDER_TYPE_CASE_LISTS => '案例列表',
        self::SLIDER_TYPE_NEWS_DETAIL => '新闻详情',
        self::SLIDER_TYPE_NEWS_LISTS => '新闻列表',
        self::SLIDER_TYPE_JOIN => '招贤纳士',
        self::SLIDER_TYPE_CASE_LETTER => '无跳转',
        self::SLIDER_TYPE_CASE_LETTER_B => '无跳转有背景条',
    ];

    /**
     * 获取计划任务队列状态名称
     * @static
     * @access public
     * @param  int $nState [in opt]类型
     * @return array|string
     */
    public static function getQueueStateName($nState = null)
    {
        if (null === $nState)
        {
            return self::$_aQueueMaps;
        }
        else
        {
            return isset(self::$_aQueueMaps[$nState]) ? self::$_aQueueMaps[$nState] : '未知';
        }
    }

    /**
     * 获取计划任务类型名称
     * @static
     * @access public
     * @param  int $nType [in opt]类型
     * @return array|string
     */
    public static function getTaskTypeName($nType = null)
    {
        if (null === $nType)
        {
            return self::$_aTaskTypeMap;
        }
        else
        {
            return isset(self::$_aTaskTypeMap[$nType]) ? self::$_aTaskTypeMap[$nType] : '未知';
        }
    }

    /**
     * 获取日志等级名称
     * @static
     * @access public
     * @param  int $nLevel [in opt]等级
     * @return array|string
     */
    public static function getLogLevelName($nLevel = null)
    {
        if (null === $nLevel)
        {
            return self::$_aLogLevelMaps;
        }
        else
        {
            return isset(self::$_aLogLevelMaps[$nLevel]) ? self::$_aLogLevelMaps[$nLevel] : '未知';
        }
    }

    /**
     * 根据学员类型获取映射名称
     * @static
     * @access public
     * @param int $nType [in opt]类型码
     * @return array|string
     */
    public static function getStudentType($nType = '')
    {
        if ($nType === '') {
            return self::$_aStudentTypeMap;
        }
        else {
            return isset(self::$_aStudentTypeMap[$nType]) ? self::$_aStudentTypeMap[$nType] : '未知';
        }
    }

    /**
     * 根据体验课类型获取映射名称
     * @static
     * @access public
     * @param int $nType [in opt]类型码
     * @return array|string
     */
    public static function getLearnTimeType($nType = '')
    {
        if ($nType === '') {
            return self::$_aLearnTimeMap;
        }
        else {
            return isset(self::$_aLearnTimeMap[$nType]) ? self::$_aLearnTimeMap[$nType] : '未知';
        }
    }

    /**
     * 根据课程类型获取映射名称
     * @static
     * @access public
     * @param int $nType [in opt]类型码
     * @return array|string
     */
    public static function getCourseType($nType = '')
    {
        if ($nType === '') {
            return self::$_aCourseTypeMap;
        }
        else {
            return isset(self::$_aCourseTypeMap[$nType]) ? self::$_aCourseTypeMap[$nType] : '未知';
        }
    }

    /**
     * 根据是否为菜单项映射获取菜单项映射名称
     * @static
     * @access public
     * @param int $nType [in opt]种类码
     * @return array|string
     */
    public static function getIsMenuType($nType = '')
    {
        if ($nType === '') {
            return self::$_aIsMenuMap;
        }
        else {
            return isset(self::$_aIsMenuMap[$nType]) ? self::$_aIsMenuMap[$nType] : '未知';
        }
    }



    /**
     * 根据经典映射获取经典种类
     * @static
     * @access public
     * @param int $nState [in opt]经典种类码
     * @return array|string 经典信息
     */
    public static function getIsClassical($nState = '')
    {
        if ($nState === '') {
            return self::$_aCasesClassicalMap;
        }
        else {
            return isset(self::$_aCasesClassicalMap[$nState]) ? self::$_aCasesClassicalMap[$nState] : '未知';
        }
    }

    /**
     * 根据推送映射获取推送种类
     * @static
     * @access public
     * @param int $nState [in opt]推送种类码
     * @return array|string 推送信息
     */
    public static function getIsSend($nState = '')
    {
        if ($nState === '') {
            return self::$_aNewsSendMap;
        }
        else {
            return isset(self::$_aNewsSendMap[$nState]) ? self::$_aNewsSendMap[$nState] : '未知';
        }
    }

    /**
     * 根据置顶映射获取置顶种类
     * @static
     * @access public
     * @param int $nState [in opt]置顶种类码
     * @return array|string 推送信息
     */
    public static function getIsTop($nState = '')
    {
        if ($nState === '') {
            return self::$_aNewsIsTop;
        }
        else {
            return isset(self::$_aNewsIsTop[$nState]) ? self::$_aNewsIsTop[$nState] : '未知';
        }
    }

    /**
     * 根据是否映射获取是否名称
     * @static
     * @access public
     * @param string $nType [in opt]种类码
     * @return array|string 推送信息
     */
    public static function getCommonIS($nType = '')
    {
        if ($nType === '') {
            return self::$_aCommonMap;
        }
        else {
            return isset(self::$_aCommonMap[$nType]) ? self::$_aCommonMap[$nType] : '未知';
        }
    }

    /**
     * 根据是否已读映射获取名称
     * @static
     * @access public
     * @param string $nType [in opt]种类码
     * @return array|string 推送信息
     */
    public static function getLogIsRead($nType = '')
    {
        if ($nType === '') {
            return self::$_aLogIsRead;
        }
        else {
            return isset(self::$_aLogIsRead[$nType]) ? self::$_aLogIsRead[$nType] : '未知';
        }
    }

    /**
     * 根据轮播图映射获取轮播图种类
     * @static
     * @access public
     * @param int $nState [in opt]轮播图种类码
     * @return array|string 轮播图信息
     */
    public static function getSliderType($nState = '')
    {
        if ($nState === '') {
            return self::$_aSliderMap;
        }
        else {
            return isset(self::$_aSliderMap[$nState]) ? self::$_aSliderMap[$nState] : '未知';
        }
    }


}