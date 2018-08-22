<?php
namespace app\music\controller;

use app\common\controller\Sys;
use think\Controller;
use think\Exception;

class Learn extends Controller
{

    /**
     * @var object 体验课模型
     */
    protected $_lModel;

    /**
     * @var object 体验课配置模型
     */
    protected $_lcModel;

    /**
     * @var object 体验课学员模型
     */
    protected $_lsModel;

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();

        $this->_lModel = new \app\common\model\Learncourse();

        $this->_lcModel = new \app\common\model\Learnconfig();

        $this->_lsModel = new \app\common\model\Learnstudent();
    }

    /**
     * 体验课列表
     * @return mixed
     */
    public function lists()
    {

        $timeData = [];

        $myLearn = [
            'id' => '',
            'name' => '',
            'phone' => '',
            'time_area' => '',
            'student_type' => Sys::STUDENT_TYPE_ADULT
        ];

        $learnLists = $this->_lModel->where(['course_type' => Sys::COURSE_TYPE_LEARN, 'is_send' => Sys::NEWS_IS_SEND])->select()->toArray();

        if (!$learnLists)
        {
            $learnData = [];
        }
        else
        {
            $learnData = $learnLists[0];
            $forward = $learnData['forward'];
            $learnDay = $learnData['long'];
            $start_at = date("Y-m-d",strtotime("+$forward day"));// 提前三天预约
            $end_at = date("Y-m-d",strtotime("+$learnDay day"));


            // 寻找日期
            $configArray = $this->_lcModel->where(['learn_id' => $learnData['id'], 'is_send' => Sys::NEWS_IS_SEND])->order('type, start_at')->select();

            $dateArray = $this->getDateFromRange($start_at, $end_at);

            // 休息日
            $playdayModel = new \app\common\model\Playday();
            $playdayData = $playdayModel->where(['learn_id' => $learnData['id']])->select();
            $playdayData = $playdayData->toArray();
            $playday = array_column($playdayData, 'date');

            foreach ($dateArray as $k => $v)
            {
                if (!in_array($v, $playday))
                {
                    $type = date('w', strtotime($v));
                    foreach ($configArray as $config)
                    {
                        // 判断周几，并且日期大于今天
                        if ($type == $config['type'] && $v > date('Y-m-d', time()))
                        {
                            $timeData[] = $v . ' ' . substr($config['start_at'], 0, -3) . '-' . substr($config['end_at'], 0, -3);
                        }
                    }
                }
            }


            //
            if (cookie('learn_student'))
            {
                $lsData = $this->_lsModel->where(['phone' => cookie('learn_student'), 'learn_id' => $learnData['id']])->find();
                if ($lsData)
                {
                    $lsData['time_area'] = substr($lsData['start_at'], 0, -3) . '-'. substr($lsData['end_at'], 11, 5);
                    $myLearn = $lsData;
                }
                else
                {
                    // 后台删除了数据
                    cookie('learn_student', null);
                }

            }

        }

        $this->assign(['data' => $learnData, 'student' => $myLearn, 'time' => json_encode($timeData, true)]);

        $student_type_map = Sys::getStudentType();
        $this->assign('student_type_map', $student_type_map);

        return $this->fetch();
    }

    /**
     * 获取指定日期段内每一天的日期
     * @param  Date  $startdate 开始日期
     * @param  Date  $enddate   结束日期
     * @return Array
     */
    private function getDateFromRange($startdate, $enddate){

        $stimestamp = strtotime($startdate);
        $etimestamp = strtotime($enddate);

        // 计算日期段内有多少天
        $days = ($etimestamp-$stimestamp)/86400+1;

        // 保存每天日期
        $date = array();

        for($i=0; $i<$days; $i++){
            $date[] = date('Y-m-d', $stimestamp+(86400*$i));
        }

        return $date;
    }

    // 学员报名体验
    public function add()
    {
        try
        {
            if (request()->isPost())
            {
                $param = $_POST;
                $learnData = $this->_lModel->getById($param['learn_id']);
                if (!$learnData)
                {
                    throw new Exception('体验课不存在');
                }

                if ($this->_lsModel->where(['phone' => $param['phone'], 'learn_id' => $learnData['id']])->count())
                {
                    cookie('learn_student', $param['phone'], 31536000);
                    throw new Exception('已参加过该体验课程');
                }

                $time_area = $param['time_area'];
                $day = substr($time_area, 0, 10);
                $start_at = $day . ' '. substr($time_area, 11, 5) . ':00';
                $end_at = $day . ' '. substr($time_area, 17) . ':00';
                $start_time = substr($time_area, 11, 5) . ':00';

                // 计算课程最大学员数量
                $learnConfigData = $this->_lcModel->where(['learn_id' => $learnData['id'], 'start_at' => $start_time])->find();
                $max_student = $learnConfigData['max_student'];// 每堂课程最大学员数量

                if ($this->_lsModel->where(['learn_id' => $learnData['id'], 'start_at' => $start_at])->count() >= $max_student)
                {
                    throw new Exception('该时段体验人数已超过上限，请选择其他时段');
                }

                $arr = ['learn_id' => $learnData['id'], 'name' => $param['name'], 'phone' => $param['phone'], 'student_type' => $param['student_type'], 'start_at' => $start_at, 'end_at' => $end_at,

                ];
                $arr['create_time'] = $arr['update_time'] = time();
                $flag = $this->_lsModel->insert($arr);
                if ($flag['code'])
                {
                    cookie('learn_student', $param['phone'], 31536000);
                }

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 取消预约
     * @return array|\think\response\Json
     */
    public function cancel()
    {
        try
        {
            $ls_id = input('ls_id', 0);

            if (!$ls_id)
            {
                throw new Exception('数据错误');
            }
            $flag = $this->_lsModel->del($ls_id);
            cookie('learn_student', null);

            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);

        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}