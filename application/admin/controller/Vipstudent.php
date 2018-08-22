<?php
namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 会员课学员控制类
 *
 * Class Casecat
 * @package app\admin\controller
 */
class Vipstudent extends Base
{

    public $_vModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_vModel = new \app\common\model\Vipstudent();
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

            $order = 'start_at';

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
                $studentModel = new \app\common\model\Students();
                $studentInfo = $studentModel->getByPhone($param['searchText']);
                if ($studentInfo)
                {
                    $where['student_id'] = $studentInfo['id'];
                }
                else
                {
                    $where['student_id'] = 0;
                }
            }

            if (isset($param['start_at']) && !empty($param['start_at'])) {
                $where['start_at'] = $param['start_at'];
            }
            else
            {
                if (isset($param['is_over']) && !empty($param['is_over'])) {
                    if ($param['is_over'] == 1)
                    {
                        $where['start_at'] = ['>=', date('Y-m-d H:i:s', time())];
                    }
                }
            }

            $selectResult = $this->_vModel->getByWhere($where, $order, $offset, $limit);

            //生成按钮
            foreach ($selectResult as $key => $vo) {
                $selectResult[$key]['student_type_name'] = Sys::getStudentType($selectResult[$key]['students']['type']);
                $operate = [
                    '编辑' => [
                        'href' => url('edit', ['id' => $vo['id']]),
                        'auth' => 'vipstudent/edit'
                    ],
                    '删除' => [
                        'href' => "javascript:del('" . $vo['id'] . "')",
                        'auth' => 'vipstudent/del'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_vModel->getAll($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    /**编辑会员课等级
     * @return array|mixed|\think\response\Json
     */
    public function edit()
    {
        try
        {
            if (request()->isPost()) {
                $param = $_POST;
                $id = $param['id'];
                $aData = $this->_vModel->getById($id);

                $arr = ['id' => $id,'grade_id' => $param['grade_id']

                ];
                $arr['update_time'] = time();
                $flag = $this->_vModel->editGrade($arr);
                if ($flag['code'] > 0)
                {
                    $gradeModel = new \app\common\model\Coursegrade();
                    $gradeNew = $gradeModel->find($param['grade_id'])['name'];
                    $this->logInfo('编辑会员课程成功，学员姓名：'. $aData['students']['name'] . ' , 课程ID：' . $aData['learn_id']. ' , 课程等级由："' . $aData['grade']['name'] .'" , 更新为："'. $gradeNew . '" , 开课时间：'. $aData['start_at']);
                }

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $id = input('param.id');

            $aData = $this->_vModel->getById($id);

            $type_map = Sys::getStudentType();
            $this->assign('type_map', $type_map);

            $this->assign('data', $aData);

            return $this->fetch();
        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }


    //删除
    public function del()
    {
        $id = input('param.id');
        $aData = $this->_vModel->where('id', $id)->find();
        $studentModel = new \app\common\model\Students();
        $studentData = $studentModel->getById($aData['student_id']);

        $flag = $this->_vModel->del($id);
        if ($flag['code'] > 0)
        {
            $this->logWarn('删除已预约会员课成功，学员姓名：'. $studentData['name'] . ' , 课程ID：' . $aData['learn_id'] . ' , 开课时间：'. $aData['start_at']);
        }

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
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