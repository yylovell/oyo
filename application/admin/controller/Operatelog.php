<?php
namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 管理员日志控制类
 *
 * Class Casecat
 * @package app\admin\controller
 */
class Operatelog extends Base
{

    public $_oModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_oModel = new \app\common\model\Operatelog();
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

                if ($param['searchText']['username']) {
                    $userModel = new \app\admin\model\UserModel();
                    $userInfo = $userModel->where('username', $param['searchText']['username'])->find();
                    if ($userInfo)
                    {
                        $where['admin_id'] = $userInfo['id'];
                    }
                    else
                    {
                        $where['admin_id'] = 0;
                    }
                }

                if ((int)$param['searchText']['is_read'] !== 1000) {
                    $where['is_read'] = $param['searchText']['is_read'];
                }

            }
            $where['delete_at'] = ['eq', '0000-00-00 00:00:00'];

            $selectResult = $this->_oModel
                ->alias('o')
                ->join('oyo_user u', 'u.id = o.admin_id')
                ->where($where)
                ->field('o.*, u.username')
                ->order('o.time desc')
                ->limit($offset, $limit)
                ->select();
            //生成按钮
            foreach ($selectResult as $key => $vo) {
                $selectResult[$key]['level'] = color(Sys::getLogLevelName($selectResult[$key]['level']), $selectResult[$key]['level'], 'width:30px;height:30px;border-radius:50%;text-align:center;line-height:30px;padding:0;margin:0;');
                $selectResult[$key]['is_read'] = Sys::getLogIsRead($selectResult[$key]['is_read']);
                $operate = [
                    '删除' => [
                        'href' => "javascript:del('" . $vo['id'] . "')",
                        'auth' => 'operatelog/del'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_oModel->getAll($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        $this->assign(['is_read_map' => Sys::getLogIsRead()]);

        return $this->fetch();
    }

    // 全部标记为已读
    public function read()
    {
        try
        {
            $flag = $this->_oModel->read();
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        catch (Exception $e)
        {
            return json(['code' => 0, 'data' => '', 'msg' => $e->getMessage()]);
        }
    }


    //删除
    public function del()
    {
        $id = input('param.id');

        $flag = $this->_oModel->del($id);

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}