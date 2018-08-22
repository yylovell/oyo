<?php
namespace app\admin\controller;

use app\admin\model\Node;
use think\Controller;

/**
 * 公共基础类
 *
 * Class Base
 * @package app\admin\controller
 */
abstract class Base extends Controller
{
    use \traits\Log;
    /**
     * 登录ID
     * @access public
     * @var int
     */
    protected $nLoginId = 0;

    /**
     * 登录信息
     * @access public
     * @var array
     */
    protected $aLoginInfo = [];


    /**
     * 初始化
     */
    public function _initialize()
    {
        if(empty(session('username'))){

            $this->redirect(url('login/index'));
        }

        // 管理员信息
        $userModel = new \app\admin\model\UserModel;
        $this->aLoginInfo = $userModel->where('username', session('username'))->find();
        $this->nLoginId = $this->aLoginInfo['id'];

        //检测权限
        $control = lcfirst( request()->controller() );
        $action = lcfirst( request()->action() );

        if(empty(authCheck($control . '/' . $action))){
            if(request()->isAjax()){
                return msg(403, '', '您没有权限');
            }

            $this->error('403,您没有权限');
        }

        //获取权限菜单
        $node = new Node();

        $this->assign([
            'username' => session('username'),
            'avatar' => $this->aLoginInfo['user_avatar'],
            'menu' => $node->getMenu(session('rule')),
            'rolename' => session('role')
        ]);

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
        $log['admin_id'] = $this->nLoginId;
        $log['keyword'] = $keyword;
        $log['time'] = date('Y-m-d H:i:s', time());
        $logModel = new \app\common\model\Operatelog;
        $logModel->insert($log);
    }
}