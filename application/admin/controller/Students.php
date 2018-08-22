<?php

namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 学生控制类
 *
 * Class User
 * @package app\admin\controller
 */
class Students extends Base
{

    public $_sModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_sModel = new \app\common\model\Students();
    }

    //学生列表
    public function lists()
    {
        if (request()->isAjax()) {
            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (isset($param['searchText'])) {
                if ($param['searchText']['name']) {
                    $where['name'] = ['like', '%' . $param['searchText']['name'] . '%'];
                }
                if ((int)$param['searchText']['is_send'] !== 1000) {
                    $where['is_send'] = $param['searchText']['is_send'];
                }
                if ((int)$param['searchText']['type'] !== 1000) {
                    $where['type'] = $param['searchText']['type'];
                }
            }

            $selectResult = $this->_sModel->getByWhere($where, $offset, $limit);

            //生成按钮
            foreach ($selectResult as $key => $vo) {
                $selectResult[$key]['user_avatar'] = '<img src="' . config('view_replace_str')['__UPLOADS__'] . '/' .$vo['user_avatar'] . '" width="40px" height="40px">';
                $selectResult[$key]['is_send'] = onButton('students/status', Sys::getIsSend($vo['is_send']), $vo['is_send'], "javascript:status('" . $vo['id'] . "' , '" . $vo['is_send'] . "')");
                $selectResult[$key]['type_name'] = Sys::getStudentType($selectResult[$key]['type']);

                // $operate = ['编辑' => url('students/edit', ['id' => $vo['id']]), '添加固定课程' => url('students/fix', ['id' => $vo['id']]), '重置密码' => "javascript:replace('" . $vo['id'] . "')", '删除' => "javascript:del('" . $vo['id'] . "')"];
                $operate = [
                    '编辑' => [
                        'href' => url('edit', ['id' => $vo['id']]),
                        'auth' => 'students/edit'
                    ],
                    '添加固定课程' => [
                        'href' => url('fix', ['id' => $vo['id']]),
                        'auth' => 'students/fix'
                    ],
                    '重置密码' => [
                        'href' => "javascript:replace('" . $vo['id'] . "')",
                        'auth' => 'students/replace'
                    ],
                    '详情' => [
                        'href' => url('detail', ['id' => $vo['id']]),
                        'auth' => 'students/detail'
                    ],
                    '删除' => [
                        'href' => "javascript:del('" . $vo['id'] . "')",
                        'auth' => 'students/del'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_sModel->getAll($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }
        $is_send_map = Sys::getIsSend();
        $type_map = Sys::getStudentType();

        $this->assign(['is_send_map' => $is_send_map, 'type_map' => $type_map]);
        return $this->fetch();
    }

    //添加
    public function add()
    {
        //
        try {
            if (request()->isPost()) {
                $param = $_POST;
                //$param = parseParams($param['data']);
                if (!is_mobile($param['phone']))
                {
                    throw new Exception('手机号格式错误');
                }

                // 检查手机号是否重复
                $count = $this->_sModel->getAll(['phone' => $param['phone']]);
                if ($count)
                {
                    throw new Exception('该手机号已注册');
                }

                $arr = ['name' => $param['name'], 'type' => $param['type'], 'password' => md5($param['password']), 'phone' => $param['phone'], 'is_send' => $param['is_send'], 'sign_time' => $param['sign_time'], 'des' => $param['des'], 'user_avatar' => $param['user_avatar']

                ];
                $arr['create_time'] = $arr['update_time'] = time();
                $flag = $this->_sModel->insertStudents($arr);
                if ($flag['code'] > 0)
                {
                    $this->logInfo('添加学员成功，学员姓名：'. $param['name']);
                }

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $type_map = Sys::getStudentType();
            $this->assign('type_map', $type_map);

            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }

    }

    //详情
    public function detail()
    {
        try {

            $id = input('param.id');
            $aData = $this->_sModel->getById($id);

            $type_map = Sys::getStudentType();
            $this->assign('type_map', $type_map);

            $this->assign('data', $aData);

            return $this->fetch();
        } catch (Exception $e) {
             $this->error($e->getMessage());
        }
    }

    //编辑
    public function edit()
    {
        try {
            if (request()->isPost()) {
                $param = $_POST;

                if (!is_mobile($param['phone']))
                {
                    throw new Exception('手机号格式错误');
                }

                // 检查手机号是否重复
                $count = $this->_sModel->getAll(['phone' => $param['phone'], 'id' => ['neq', $param['id']]]);
                if ($count)
                {
                    throw new Exception('该手机号已注册');
                }

                $arr = ['id' => $param['id'], 'name' => $param['name'], 'type' => $param['type'], 'phone' => $param['phone'], 'is_send' => $param['is_send'], 'sign_time' => $param['sign_time'], 'des' => $param['des']

                ];
                $arr['update_time'] = time();
                $flag = $this->_sModel->editStudents($arr);
                if ($flag['code'] > 0)
                {
                    $this->logInfo('编辑学员成功，学员姓名：'. $param['name']);
                }

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $id = input('param.id');
            $aData = $this->_sModel->getById($id);

            $type_map = Sys::getStudentType();
            $this->assign('type_map', $type_map);

            $this->assign('data', $aData);

            return $this->fetch();
        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 启用
     * @param $aData
     * @return \think\response\Json
     * @throws Exception
     */
    private function _send($aData)
    {
        $id = $aData['id'];

        $param = [
            'id' => $id,
            'is_send' => Sys::NEWS_IS_SEND
        ];

        $flag = $this->_sModel->send($param);
        if ($flag['code'] > 0)
        {
            $this->logInfo('启用学员成功，学员ID：'. $id);
        }

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 停用
     * @param $aData
     * @return \think\response\Json
     * @throws Exception
     */
    private function _dissend($aData)
    {
        $id = $aData['id'];

        $param = [
            'id' => $id,
            'is_send' => Sys::NEWS_NOT_SEND
        ];

        $flag = $this->_sModel->send($param);
        if ($flag['code'] > 0)
        {
            $this->logWarn('停用学员成功，学员ID：'. $id);
        }

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 修改状态
     * @return \think\response\Json
     */
    public function status()
    {
        try {
            $id = input('param.id');

            $learnData = $this->_sModel->getById($id)->toArray();
            if (!$learnData)
            {
                throw new Exception('学员不存在');
            }

            if ($learnData['is_send'] == Sys::NEWS_IS_SEND)
            {
                return $this->_dissend($learnData);
            }
            else
            {
                return $this->_send($learnData);
            }

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
            return json(['code' => 0, 'data' => '', 'msg' => $info]);
        }
    }

    /**
     * 重置密码
     * @return \think\response\Json
     */
    public function replace()
    {
        $id = input('param.id');
        $aData = $this->_sModel->where('id', $id)->find();

        $param = [
            'id' => $id,
            'password' => md5('123456')
        ];

        $flag = $this->_sModel->replacePassword($param);
        if ($flag['code'] > 0)
        {
            $this->logInfo('重置学员密码成功，学员姓名：'. $aData['name']);
        }

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    //删除
    public function del()
    {
        $id = input('param.id');
        $aData = $this->_sModel->where('id', $id)->find();

        $flag = $this->_sModel->del($id);
        if ($flag['code'] > 0)
        {
            $this->logWarn('删除学员成功，学员姓名：'. $aData['name']);
        }

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * 添加学员固定课程
     * @return array|mixed|\think\response\Json
     */
    public function fix()
    {
        $vsModel = new \app\common\model\Vipstudent();
        $lcModel = new \app\common\model\Learnconfig();
        $lModel = new \app\common\model\Learncourse();
        try {
            if (request()->isPost()) {
                $param = $_POST;
                if (!$param['student_id'] || !$param['learn_id'])
                {
                    throw new Exception('数据缺失');
                }
                if (!$param['grade_id'])
                {
                    throw new Exception('请选择一个课程等级');
                }
                if (!$param['start_at'] || !$param['end_at'])
                {
                    throw new Exception('课程开始与结束时间都不能为空');
                }

                $start_at = $param['start_at'];
                $end_at = $param['end_at'];
                $start_time = substr($start_at, 11);

                if ($vsModel->where(['student_id' => $param['student_id'], 'start_at' => $start_at])->count())
                {
                    throw new Exception('该学员在该时间已经存在一门课程');
                }

                // 计算课程最大学员数量
                $learnConfigData = $lcModel->where(['learn_id' => $param['learn_id'], 'start_at' => $start_time])->find();
                if (!$learnConfigData)
                {
                    throw new Exception('该课程在该时段没有课程配置');
                }
                $online_num = $learnConfigData['online_num'];// 课程同一时段同时在线课程数
                $max_student = $learnConfigData['max_student'];// 每堂课程最大学员数量

                $maxStudents = $online_num * $max_student;

                if ($vsModel->where(['learn_id' => $param['learn_id'], 'start_at' => $start_at])->count() >= $maxStudents)
                {
                    throw new Exception('该时段上课人数已超过上限，请选择其他时段');
                }
                else
                {
                    if ($vsModel->where(['learn_id' => $param['learn_id'], 'start_at' => $start_at, 'grade_id' => $param['grade_id']])->find())
                    {
                        if ($vsModel->where(['learn_id' => $param['learn_id'], 'start_at' => $start_at, 'grade_id' => $param['grade_id']])->count() >= $max_student)
                        {
                            throw new Exception('该时段该课程等级，上课人数已超过上限，请选择其他时段，或者其他等级');
                        }
                    }
                    else
                    {
                        $vipGroupByGrade = $vsModel->where(['learn_id' => $param['learn_id'], 'start_at' => $start_at])->field('grade_id')->group('grade_id')->select();
                        $all = count($vipGroupByGrade);

                        if ($all >= $online_num)
                        {
                            throw new Exception('该时段课程等级类型已经达到最大值');
                        }
                    }
                }


                $arr = ['student_id' => $param['student_id'], 'learn_id' => $param['learn_id'], 'grade_id' => $param['grade_id'], 'start_at' => $start_at, 'end_at' => $end_at

                ];
                $arr['create_time'] = $arr['update_time'] = time();

                $flag = $vsModel->insert($arr);

                $studentData = $this->_sModel->getById($param['student_id']);
                $cgModel = new \app\common\model\Coursegrade();
                $cgData = $cgModel->where('id', $param['grade_id'])->find();
                if ($flag['code'] > 0)
                {
                    $this->logInfo('给学员添加固定课程成功，学员姓名：'. $studentData['name'] . ' , 课程ID：' . $param['learn_id'] . ' , 课程等级：' . $cgData['name'] . ' , 开课时间：' . $start_at);
                }

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }
            // 学员信息
            $student_id = input('id', 0);
            if (!$student_id)
            {
                throw new Exception('学员信息错误');
            }
            $studentData = $this->_sModel->getById($student_id);
            $this->assign('student', $studentData);

            // 会员课信息
            $lData = $lModel->where(['is_send' => Sys::NEWS_IS_SEND, 'course_type' => Sys::COURSE_TYPE_VIP])->select();
            $this->assign('learns', $lData);

            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 获取课程等级
     * @return \think\response\Json
     */
    public function getGrade()
    {
        try
        {
            if (request()->isAjax())
            {
                // 所有等级
                $cgModel = new \app\common\model\Coursegrade();
                $nodes = $cgModel->getSendList();
                $nodes = getTree(objToArray($nodes), false);

                return json(msg(1, $nodes, 'ok'));
            }

        }
        catch (Exception $e)
        {
            return json(msg(0, '', 'ok'));
        }
    }

}
