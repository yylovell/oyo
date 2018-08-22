<?php
namespace app\admin\controller;

use think\Exception;

/** 休息日控制类
 *
 * Class Casecat
 * @package app\admin\controller
 */
class Playday extends Base
{

    public $_pModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_pModel = new \app\common\model\Playday();
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
                $where['learn_id'] = $param['searchText'];
            }

            $selectResult = $this->_pModel
                ->alias('p')
                ->join('oyo_learn_course l', 'l.id = p.learn_id')
                ->where($where)
                ->field('p.*, l.title')
                ->order('learn_id, date desc')
                ->limit($offset, $limit)
                ->select();

            //生成按钮
            foreach ($selectResult as $key => $vo) {
                $operate = [
                    '详情' => [
                        'href' => url('detail', ['id' => $vo['id']]),
                        'auth' => 'playday/detail'
                    ],
                    '删除' => [
                        'href' => "javascript:del('" . $vo['id'] . "')",
                        'auth' => 'playday/del'
                    ]
                ];

                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_pModel->getAll($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    //添加
    public function add()
    {
        //
        try {
            if (request()->isPost()) {
                $param = $_POST;

                if ($this->_pModel->where(['learn_id' => $param['learn_id'], 'date' => $param['date']])->count())
                {
                    throw new Exception('该课程该休息日已经存在');
                }

                $arr = ['learn_id' => $param['learn_id'], 'date' => $param['date'], 'des' => $param['des'],

                ];
                $arr['create_time'] = $arr['update_time'] = time();

                $flag = $this->_pModel->insert($arr);

                if ($flag['code'] > 0)
                {
                    $this->logInfo('添加休息日成功，休息日期：'. $param['date'] . ' , 课程ID：' . $param['learn_id']);
                }

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $LearnModel = new \app\common\model\Learncourse();
            $lData = $LearnModel->field('id, title')->select();


            $this->assign(['learns' => $lData]);

            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }

    }

    // 详情
    public function detail()
    {
        try {

            $id = input('param.id');
            $aData = $this->_pModel->getById($id);

            $LearnModel = new \app\common\model\Learncourse();
            $lData = $LearnModel->field('id, title')->select();


            $this->assign(['data' => $aData, 'learns' => $lData]);

            return $this->fetch();

        } catch (Exception $e) {
            $info = $e->getMessage();
            $this->error($info);
        }
    }


    //删除
    public function del()
    {
        $id = input('param.id');
        $aData = $this->_pModel->where('id', $id)->find();

        $flag = $this->_pModel->del($id);
        if ($flag['code'] > 0)
        {
            $this->logWarn('删除休息日成功， 休息日期：'. $aData['date'] . ' , 课程ID：' . $aData['learn_id']);
        }

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}