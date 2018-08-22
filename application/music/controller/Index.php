<?php
namespace app\music\controller;

use app\common\controller\Sys;
use think\console\Input;
use think\Exception;

class Index extends Base
{

    /**
     * @var object 课程模型
     */
    protected $_lModel;

    /**
     * @var object 课程配置模型
     */
    protected $_lcModel;

    /**
     * @var object 课程学员模型
     */
    protected $_vsModel;

    /**
     * @var object 课程等级模型
     */
    protected $_cgModel;

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();

        $this->_lModel = new \app\common\model\Learncourse();

        $this->_lcModel = new \app\common\model\Learnconfig();

        $this->_vsModel = new \app\common\model\Vipstudent();

        $this->_cgModel = new \app\common\model\Coursegrade();
    }

    /**
     * 首页
     * @return mixed
     */
    public function index()
    {

        $this->redirect('lists');
    }

    /**
     * 会员课列表
     * @return mixed
     */
    public function lists()
    {
        $learnLists = $this->_lModel
            ->where(['course_type' => Sys::COURSE_TYPE_VIP, 'is_send' => Sys::NEWS_IS_SEND])
            ->select();

        foreach ($learnLists as &$v)
        {
            $vipLearnData = $this->_vsModel->with(['grade'])->where(['student_id' => $this->_student['id'], 'learn_id' => $v['id']])->order('start_at desc')->limit(1)->select()->toArray();
            if ($vipLearnData)
            {
                $v['info'] = '最近：' . substr($vipLearnData[0]['start_at'], 0, 16) . ' 等级：' . trim($vipLearnData[0]['grade']['name']);
            }
            else
            {
                $v['info'] = '';
            }
        }
        unset($v);

        $this->assign('data' , $learnLists);


        return $this->fetch();
    }

    /**
     * 会员课详情
     * @return mixed
     */
    public function detail()
    {
        $learn_id = input('learn_id', 0);
        $timeData = [];

        $learnData = $this->_lModel
            ->where(['id' => $learn_id])
            ->find();

        $forward = $learnData['forward'];
        $learnDay = $learnData['long'];
        $start_at = date("Y-m-d",strtotime("+$forward day"));
        $end_at = date("Y-m-d",strtotime("+$learnDay day"));

        // 寻找日期
        $configArray = $this->_lcModel->where(['learn_id' => $learnData['id'], 'is_send' => Sys::NEWS_IS_SEND])->order('type, start_at')->select();

        $dateArray = $this->getDateFromRange($start_at, $end_at);

        // 休息日
        $playdayModel = new \app\common\model\Playday();
        $playdayData = $playdayModel->where(['learn_id' => $learn_id])->select();
        $playdayData = $playdayData->toArray();
        $playday = array_column($playdayData, 'date');

        foreach ($dateArray as $k => $v)
        {
            if (!in_array($v, $playday)) // 不是休息日
            {
                $type = date('w', strtotime($v));
                foreach ($configArray as $config)
                {
                    // 判断周几，并且日期大于今天
                    if ($type == $config['type'] && $v > date('Y-m-d', time()))
                    {
                        if((strtotime($v . ' ' . $config['start_at'])-strtotime(date('Y-m-d H:i:s', time()))) > 86400*$forward)
                        {
                            $timeData[] = $v . ' ' . substr($config['start_at'], 0, -3) . '-' . substr($config['end_at'], 0, -3);
                        }

                    }
                }
            }
        }

        $this->assign(['data' => $learnData, 'time' => json_encode($timeData, true)]);


        return $this->fetch();
    }

    /**
     * 寻找等级
     * @return array
     */
    public function getGrade()
    {

        try
        {
            if (request()->isPost())
            {
                $learn_id = input('learn_id', 0);
                $time_area = input('time_area', '');
                if (!$learn_id || !$time_area)
                {
                    throw new Exception('数据错误');
                }

                $learnData = $this->_lModel
                    ->where(['id' => $learn_id])
                    ->find();

                $day = substr($time_area, 0, 10);
                $start_at = substr($time_area, 11, 5) . ':00';
                $end_at = substr($time_area, 17) . ':00';

                // 课程同一时段同时在线课程数
                $learnConfigData = $this->_lcModel->where(['learn_id' => $learn_id, 'start_at' => $start_at])->find();
                $online_num = $learnConfigData['online_num'];

                // 课程该时段已经报名课程数（按等级分组）
                $learnVipStudent = $this->_vsModel->where(['learn_id' => $learn_id, 'start_at' => $day . ' '. $start_at])->field('grade_id')->group('grade_id')->select();

                $count = count($learnVipStudent);

                // 所有等级
                $nodes = $this->_cgModel->getSendList();
                $nodes = getTree(objToArray($nodes), false);

                // 课程可用等级
                $grade_ids = explode(',', $learnData['grade_ids']);
                $nodes_learn = [];
                foreach ($nodes as $node)
                {
                    if (in_array($node['id'], $grade_ids))
                    {
                        $nodes_learn[] = $node;
                    }
                }
                $newNode = [];//显示等级

                if (!$learnVipStudent)
                {
                    $newNode = $nodes_learn;
                }
                else
                {
                    if ($count < $online_num)
                    {
                        $newNode = $nodes_learn;
                    }
                    else
                    {
                        $newNode2=[];
                        foreach ($learnVipStudent as $item)
                        {
                            $grade_id = $item['grade_id'];
                            $gradeData = $this->_cgModel->where(['id' => $grade_id])->field('id, type_id')->find();
                            foreach ($nodes_learn as $v)
                            {
                                if ($v['id'] == $gradeData['type_id'])
                                {
                                    foreach ($v['children'] as $k=>$c)
                                    {
                                        if ($c['id'] != $grade_id)
                                        {
                                            unset($v['children'][$k]);
                                        }
                                    }
                                    $newNode2[] = $v;
                                }
                            }
                        }
                        // 折叠重复
                        $newNode = $this->combine_same_val($newNode2);
                    }
                }

                //dump($newNode);exit;
                $this->assign('grades', $newNode);
                return ['code' => 1, 'data' => $this->fetch(), 'msg' => '获取成功'];
            }

        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     **关于参数的说明
     **$array代表原数组
     **$start代表$array[0][$key]
     **/
    public function combine_same_val($array){
        static $new;
        $start = $array[0]['id'];

        foreach($array as $k=>$v){
            if($v['id']==$start){
                $new[$v['id']]['id'] = $v['id'];
                $new[$v['id']]['name'] = $v['name'];
                $new[$v['id']]['des'] = $v['des'];
                $new[$v['id']]['pid'] = $v['pid'];
                foreach ($v['children'] as $item)
                {
                    $new[$v['id']]['children'][] = $item;
                }
                unset($array[$k]);
                continue;
            }
        }
        sort($array);
        if(count($array)!==0){
            $this->combine_same_val($array);
        }
        return $new;
    }

    // 学员报名体验
    public function add()
    {
        try
        {
            if (request()->isPost())
            {
                $param = $_POST;

                $time_area = $param['time_area'];
                $day = substr($time_area, 0, 10);
                $start_at = $day . ' '. substr($time_area, 11, 5) . ':00';
                $end_at = $day . ' '. substr($time_area, 17) . ':00';
                $start_time = substr($time_area, 11, 5) . ':00';

                $learnData = $this->_lModel->getById($param['learn_id']);
                if (!$learnData)
                {
                    throw new Exception('课程不存在');
                }

                if ($this->_vsModel->where(['student_id' => $this->_student['id'], 'learn_id' => $learnData['id'], 'start_at' => $start_at])->count())
                {
                    throw new Exception('该时段已有预约的课程');
                }

                // 计算课程最大学员数量
                $this->_calCount($learnData['id'], $param['grade_id'], $start_at, $start_time);

                $arr = ['learn_id' => $learnData['id'], 'student_id' => $this->_student['id'], 'grade_id' => $param['grade_id'], 'start_at' => $start_at, 'end_at' => $end_at,

                ];
                $arr['create_time'] = $arr['update_time'] = time();
                $flag = $this->_vsModel->insert($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**计算对应课程对应等级是否满员
     * @param $learn_id
     * @param $grade_id
     * @param $start_at
     * @param $start_time
     * @throws Exception
     */
    private function _calCount($learn_id, $grade_id, $start_at, $start_time)
    {
        // 计算课程最大学员数量
        $learnConfigData = $this->_lcModel->where(['learn_id' => $learn_id, 'start_at' => $start_time])->find();
        $online_num = $learnConfigData['online_num'];// 课程同一时段同时在线课程数
        $max_student = $learnConfigData['max_student'];// 每堂课程最大学员数量

        $maxStudents = $online_num * $max_student;

        if ($this->_vsModel->where(['learn_id' => $learn_id, 'start_at' => $start_at])->count() >= $maxStudents)
        {
            throw new Exception('该时段上课人数已超过上限，请选择其他时段');
        }
        else
        {
            if ($this->_vsModel->where(['learn_id' => $learn_id, 'start_at' => $start_at, 'grade_id' => $grade_id])->count() >= $max_student)
            {
                throw new Exception('该时段该课程等级，上课人数已超过上限，请选择其他时段，或者其他等级');
            }
        }
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

}