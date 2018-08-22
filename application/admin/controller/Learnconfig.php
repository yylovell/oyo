<?php
namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 体验课配置控制类
 *
 * Class Casecat
 * @package app\admin\controller
 */
class Learnconfig extends Base
{

    public $_lcModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_lcModel = new \app\common\model\Learnconfig();
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

            $sort = $param['sortName'];
            $sort_order = $param['sortOrder'];

            $sortArr = explode(',', $sort);
            $sortOrderArr = explode(',', $sort_order);

            $order = 'learn_id, type, start_at';

            if ($sortArr)
            {
                $order = [];
                foreach ($sortArr as $k=>$v)
                {
                    $order[trim($v)] = trim($sortOrderArr[$k]);
                }
            }

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['learn_id'] = $param['searchText'];
            }

            $selectResult = $this->_lcModel
                ->alias('lc')
                ->join('oyo_learn_course l', 'l.id = lc.learn_id')
                ->where($where)
                ->field('lc.*, l.title')
                ->order($order)
                ->limit($offset, $limit)
                ->select();

            //生成按钮
            foreach ($selectResult as $key => $vo) {
                $selectResult[$key]['is_send'] = onButton('learnconfig/status', Sys::getIsSend($vo['is_send']), $vo['is_send'], "javascript:status('" . $vo['id'] . "' , '" . $vo['is_send'] . "')");
                $selectResult[$key]['type'] = Sys::getLearnTimeType($selectResult[$key]['type']);

                // $operate = ['编辑' => url('edit', ['id' => $vo['id']]), '删除' => "javascript:del('" . $vo['id'] . "')"];
                $operate = [
                    '编辑' => [
                        'href' => url('edit', ['id' => $vo['id']]),
                        'auth' => 'learnconfig/edit'
                    ],
                    '详情' => [
                        'href' => url('detail', ['id' => $vo['id']]),
                        'auth' => 'learnconfig/detail'
                    ],
                    '删除' => [
                        'href' => "javascript:del('" . $vo['id'] . "')",
                        'auth' => 'learnconfig/del'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_lcModel->getAll($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    //添加
    public function add()
    {
        $LearnModel = new \app\common\model\Learncourse();
        //
        try {
            if (request()->isPost()) {
                $param = $_POST;

                $time_area = $param['time_area'];
                $start_at = substr($time_area, 0, 8);
                $end_at = substr($time_area, 11);
                if ($this->_lcModel->where(['learn_id' => $param['learn_id'], 'type' => $param['type'], 'start_at' => $start_at])->count())
                {
                    throw new Exception('该课程该时段已经存在配置信息');
                }

                $arr = ['learn_id' => $param['learn_id'], 'is_send' => $param['is_send'], 'online_num' => $param['online_num'], 'max_student' => $param['max_student'], 'start_at' => $start_at, 'end_at' => $end_at, 'type' => $param['type'],

                ];
                $arr['create_time'] = $arr['update_time'] = time();

                $flag = $this->_lcModel->insert($arr);

                if ($flag['code'] > 0)
                {
                    $this->logInfo('添加配置成功，配置ID：'. $flag['data'] . ' , 课程ID：' . $param['learn_id']);
                }

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $lData = $LearnModel->field('id, title, course_type')->select();
            $auto_course_type = 0;
            if ($lData)
            {
                $auto_course_type = $lData[0]['course_type'];
            }

            $type_map = Sys::getLearnTimeType();

            $this->assign(['type_map' => $type_map, 'learns' => $lData, 'auto_course_type' => $auto_course_type]);

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
            $aData = $this->_lcModel->getById($id);
            $time_area = $aData['start_at'] . ' - ' . $aData['end_at'];
            $aData['time_area'] = $time_area;

            $LearnModel = new \app\common\model\Learncourse();
            $lData = $LearnModel->field('id, title, course_type')->select();

            $auto_course_type = 0;
            foreach ($lData as $v)
            {
                if($v['id'] == $aData['learn_id'])
                {
                    $auto_course_type = $v['course_type'];
                }
            }

            $type_map = Sys::getLearnTimeType();

            $this->assign(['data' => $aData, 'type_map' => $type_map, 'learns' => $lData, 'auto_course_type' => $auto_course_type]);

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

                $time_area = $param['time_area'];
                $start_at = substr($time_area, 0, 8);
                $end_at = substr($time_area, 11);

                if ($this->_lcModel->where(['learn_id' => $param['learn_id'], 'type' => $param['type'], 'start_at' => $start_at, 'id' => ['neq', $param['id']]])->count())
                {
                    throw new Exception('该课程该时段已经存在配置信息');
                }

                $arr = ['id' => $param['id'], 'is_send' => $param['is_send'], 'learn_id' => $param['learn_id'], 'online_num' => $param['online_num'], 'max_student' => $param['max_student'], 'start_at' => $start_at, 'end_at' => $end_at, 'type' => $param['type'],

                ];
                $arr['update_time'] = time();
                $flag = $this->_lcModel->edit($arr);

                if ($flag['code'] > 0)
                {
                    $this->logInfo('编辑配置成功，配置ID：'. $param['id'] . ' , 课程ID：' . $param['learn_id']);
                }

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $id = input('param.id');
            $aData = $this->_lcModel->getById($id);
            $time_area = $aData['start_at'] . ' - ' . $aData['end_at'];
            $aData['time_area'] = $time_area;

            $LearnModel = new \app\common\model\Learncourse();
            $lData = $LearnModel->field('id, title, course_type')->select();

            $auto_course_type = 0;
            foreach ($lData as $v)
            {
                if($v['id'] == $aData['learn_id'])
                {
                    $auto_course_type = $v['course_type'];
                }
            }

            $type_map = Sys::getLearnTimeType();

            $this->assign(['data' => $aData, 'type_map' => $type_map, 'learns' => $lData, 'auto_course_type' => $auto_course_type]);

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

        $flag = $this->_lcModel->send($param);
        if ($flag['code'] > 0)
        {
            $this->logInfo('启用课程配置成功，配置ID：'. $id);
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

        $flag = $this->_lcModel->send($param);
        if ($flag['code'] > 0)
        {
            $this->logWarn('停用课程配置成功，配置ID：'. $id);
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

            $learnData = $this->_lcModel->getById($id)->toArray();
            if (!$learnData)
            {
                throw new Exception('课程配置不存在');
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
     * 查找课程信息
     * @return array|\think\response\Json
     */
    public function getlearn()
    {
        try {
            if (request()->isGet()) {
                $learn_id = input('learn_id', 0);
                if (!$learn_id)
                {
                    throw new Exception('课程ID缺失');
                }

                $LearnModel = new \app\common\model\Learncourse();
                $learnData = $LearnModel->getById($learn_id);

                return json(['code' => 1, 'data' => $learnData, 'msg' => '查找成功']);
            }
        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    //删除
    public function del()
    {
        $id = input('param.id');
        $aData = $this->_lcModel->where('id', $id)->find();

        $flag = $this->_lcModel->del($id);

        if ($flag['code'] > 0)
        {
            $this->logWarn('删除配置成功，配置日期：'. Sys::getLearnTimeType($aData['type']) . ' , 开始时间'. $aData['start_at'] . ' , 课程ID：' . $aData['learn_id']);
        }

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}