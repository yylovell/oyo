<?php
namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 体验课学员控制类
 *
 * Class Casecat
 * @package app\admin\controller
 */
class Learnstudent extends Base
{

    public $_lsModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_lsModel = new \app\common\model\Learnstudent();
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

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['name'] = ['like', '%' . $param['searchText'] . '%'];
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

            $selectResult = $this->_lsModel
                ->alias('ls')
                ->join('oyo_learn_course lc', 'lc.id = ls.learn_id')
                ->where($where)
                ->field('ls.*, lc.title')
                ->order('start_at')
                ->limit($offset, $limit)
                ->select();

            //生成按钮
            foreach ($selectResult as $key => $vo) {
                $selectResult[$key]['student_type_name'] = Sys::getStudentType($selectResult[$key]['student_type']);
                // $operate = ['删除' => "javascript:del('" . $vo['id'] . "')"];
                $operate = [
                    '删除' => [
                        'href' => "javascript:del('" . $vo['id'] . "')",
                        'auth' => 'learnstudent/del'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_lsModel->getAll($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }


    //删除
    public function del()
    {
        $id = input('param.id');
        $aData = $this->_lsModel->where('id', $id)->find();

        $flag = $this->_lsModel->del($id);
        if ($flag['code'] > 0)
        {
            $this->logWarn('删除已预约体验课成功，学员姓名：'. $aData['name'] . ' , 课程ID：' . $aData['learn_id'] . ' , 开课时间：'. $aData['start_at']);
        }

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}