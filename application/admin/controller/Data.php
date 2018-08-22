<?php
namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 数据备份与还原、轮播图、访客记录、基础设置控制类
 *
 * Class Data
 * @package app\admin\controller
 */
class Data extends Base
{
    public $_sModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_sModel = new \app\admin\model\Slider();
    }

    //备份首页列表
    public function index()
    {
        $tables = db()->query('show tables');
        $tables = $tables->toArray();
        foreach($tables as $key=>$vo){

            $sql = "select count(0) as alls from " . $vo['Tables_in_' . config('database')['database']];
            $tables[$key]['alls'] = db()->query($sql)['0']['alls'];

            $operate = [
                '备份' => [
                    'auth' => 'data/importdata',
                    'href' => "javascript:importData('". $vo['Tables_in_' . config('database')['database']]."', ".$tables[$key]['alls'].")",
                ],
                '还原' => [
                    'auth' => 'data/backdata',
                    'href' => "javascript:backData('" . $vo['Tables_in_' . config('database')['database']] . "')"
                ]
            ];
            $tables[$key]['operate'] = showOperate($operate);
            if(file_exists(config('back_path') . $vo['Tables_in_' . config('database')['database']] . ".sql")){
                $tables[$key]['ctime'] = date('Y-m-d H:i:s', filemtime(config('back_path') . $vo['Tables_in_' . config('database')['database']] . ".sql"));
            }else{
                $tables[$key]['ctime'] = '无';
            }

        }
        $this->assign([
           'tables' => $tables
        ]);

        return $this->fetch();
    }

    //备份数据
    public function importData()
    {
        set_time_limit(0);
        $table = input('param.table');

        $sqlStr = "SET FOREIGN_KEY_CHECKS=0;\r\n";
        $sqlStr .= "DROP TABLE IF EXISTS `$table`;\r\n";
        $create = db()->query('show create table ' . $table);
        $sqlStr .= $create['0']['Create Table'] . ";\r\n";
        $sqlStr .= "\r\n";

        $result = db()->query('select * from ' . $table);
        foreach($result as $key=>$vo){
            $keys = array_keys($vo);
            $keys = array_map('addslashes', $keys);
            $keys = join('`,`', $keys);
            $keys = "`" . $keys . "`";
            $vals = array_values($vo);
            $vals = array_map('addslashes', $vals);
            $vals = join("','", $vals);
            $vals = "'" . $vals . "'";
            $sqlStr .= "insert into `$table`($keys) values($vals);\r\n";
        }

        $filename = config('back_path') . $table . ".sql";
        $fp = fopen($filename, 'w');
        fputs($fp, $sqlStr);
        fclose($fp);

        $this->logInfo('备份数据，表名：'. $table);
        return json(['code' => 1, 'data' => '', 'msg' => 'success']);
    }

    //还原数据
    public function backData()
    {
        set_time_limit(0);
        $table = input('param.table');

        if(!file_exists(config('back_path') . $table . ".sql")){
            return json(['code' => -1, 'data' => '', 'msg' => '备份数据不存在!']);
        }

        $sqls = analysisSql(config('back_path') . $table . ".sql");
        foreach($sqls as $key=>$sql){
            db()->query($sql);
        }
        $this->logInfo('还原数据，表名：'. $table);
        return json(['code' => 1, 'data' => '', 'msg' => 'success']);
    }

    /**
     * 轮播图
     */
    public function slider()
    {
        if (request()->isAjax()) {

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['title'] = ['like', '%' . $param['searchText'] . '%'];
            }


            $selectResult = $this->_sModel->getSliderByWhere($where, $offset, $limit);

            //生成按钮
            foreach ($selectResult as $key => $vo) {

                $selectResult[$key]['is_send'] = Sys::getIsSend($selectResult[$key]['is_send']);
                $selectResult[$key]['type'] = Sys::getSliderType($selectResult[$key]['type']);
                $selectResult[$key]['create_time'] = date('Y-m-d H:i:s',$selectResult[$key]['create_time']);
                // $operate = ['编辑' => url('data/sliderEdit', ['id' => $vo['id']]), '删除' => "javascript:sliderDel('" . $vo['id'] . "')"];
                $operate = [
                    '编辑' => [
                        'href' => url('data/sliderEdit', ['id' => $vo['id']]),
                        'auth' => 'data/slideredit'
                    ],
                    '删除' => [
                        'href' => "javascript:sliderDel('" . $vo['id'] . "')",
                        'auth' => 'data/sliderdel'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_sModel->getAllSlider($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    //添加轮播图
    public function sliderAdd()
    {
        //
        try {
            $cModel = new \app\admin\model\Cases();
            $nModel = new \app\admin\model\News();

            if (request()->isPost()) {
                $param = input();
                $id = $param['id'];

                $arr = ['title' => $param['title'], 'weight_val' => $param['weight_val'], 'is_send' => $param['is_send'], 'type' => $param['type'], 'b_color' => $param['b_color'], 'des' => $param['des'], 'photo' => $param['photo']

                ];


                switch ($param['type'])
                {
                    case Sys::SLIDER_TYPE_CASE_DETAIL:
                        $arr['url'] = url('cases/detail@www', ['id' => $id]);
                        break;
                    case Sys::SLIDER_TYPE_CASE_LISTS:
                        $arr['url'] = url('cases/lists@www');
                        break;
                    case Sys::SLIDER_TYPE_CASE_LETTER:
                        $arr['url'] = 'javascript:;';
                        break;
                    case Sys::SLIDER_TYPE_CASE_LETTER_B:
                        $arr['url'] = 'javascript:;';
                        break;
                    case Sys::SLIDER_TYPE_NEWS_DETAIL:
                        $arr['url'] = url('news/detail@www', ['id' => $id]);
                        break;
                    case Sys::SLIDER_TYPE_NEWS_LISTS:
                        $arr['url'] = url('news/lists@www');
                        break;
                    case Sys::SLIDER_TYPE_JOIN:
                        $arr['url'] = url('join/lists@www');
                        break;
                    default :
                        throw new Exception('未知类型');
                }

                $arr['create_time'] = $arr['update_time'] = time();

                $flag = $this->_sModel->insertSlider($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }


            $cData = $cModel->where(['is_classical' => Sys::CASES_IS_CLASSICAL])->field('id, title, customer')->select();
            $nData = $nModel->where(['is_send' => Sys::NEWS_IS_SEND])->field('id, title')->select();

            $type_name_map = Sys::getSliderType();

            $this->assign(['type_name_map' => $type_name_map, 'cases' => $cData, 'news' => $nData]);

            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }

    }

    /**
     * 取消新增时，判断是否上传了图片，有，则删除
     * @return string
     */
    public function cancelAdd()
    {
        try {

            $photo_path = input('photo_path');
            if ($photo_path) {

                $count = $this->_sModel->getPhotoPath($photo_path);

                if (!$count) {
                    if(is_file(ROOT_PATH . 'public/uploads/' . $photo_path)){
                        unlink(ROOT_PATH . 'public/uploads/' . $photo_path);
                    }
                }
            }
            return json(['code' => 1, 'data' => '', 'msg' => '删除成功']);

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
        }
    }

    //编辑轮播图
    public function sliderEdit()
    {
        $cModel = new \app\admin\model\Cases();
        $nModel = new \app\admin\model\News();
        try{
            if (request()->isPost()) {
                $param = input();
                $id = $param['id'];//案例或者新闻的ID
                $sid = $param['sid'];//轮播图ID

                $arr = ['id' => $sid, 'title' => $param['title'], 'weight_val' => $param['weight_val'], 'is_send' => $param['is_send'], 'type' => $param['type'], 'b_color' => $param['b_color'],  'des' => $param['des'], 'photo' => $param['photo']

                ];

                switch ($param['type'])
                {
                    case Sys::SLIDER_TYPE_CASE_DETAIL:
                        $arr['url'] = url('cases/detail@www', ['id' => $id]);
                        break;
                    case Sys::SLIDER_TYPE_CASE_LISTS:
                        $arr['url'] = url('cases/lists@www');
                        break;
                    case Sys::SLIDER_TYPE_CASE_LETTER:
                        $arr['url'] = 'javascript:;';
                        break;
                    case Sys::SLIDER_TYPE_CASE_LETTER_B:
                        $arr['url'] = 'javascript:;';
                        break;
                    case Sys::SLIDER_TYPE_NEWS_DETAIL:
                        $arr['url'] = url('news/detail@www', ['id' => $id]);
                        break;
                    case Sys::SLIDER_TYPE_NEWS_LISTS:
                        $arr['url'] = url('news/lists@www');
                        break;
                    case Sys::SLIDER_TYPE_JOIN:
                        $arr['url'] = url('join/lists@www');
                        break;
                    default :
                        throw new Exception('未知类型');
                }

                $arr['update_time'] = time();

                $flag = $this->_sModel->editSlider($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $id = input('param.id');
            $sliderData = $this->_sModel->getSliderById($id);
            $url = $sliderData['url'];
            $urlArr = explode('/', $url);
            $type = $sliderData['type'];

            $casesData = [];
            if($type == Sys::SLIDER_TYPE_CASE_DETAIL){
                $cases_id = explode('.', $urlArr[6])[0];
                $casesData = $cModel->getCasesById($cases_id);
            }

            $newsData = [];
            if($type == Sys::SLIDER_TYPE_NEWS_DETAIL){
                $news_id = explode('.', $urlArr[6])[0];
                $newsData = $nModel->getNewsById($news_id);
            }

            $cData = $cModel->where(['is_classical' => Sys::CASES_IS_CLASSICAL])->field('id, title, customer')->select();
            $nData = $nModel->where(['is_send' => Sys::NEWS_IS_SEND])->field('id, title')->select();

            $type_name_map = Sys::getSliderType();

            $this->assign(['data' => $sliderData, 'type_name_map' => $type_name_map, 'case' => $casesData, 'new' => $newsData, 'cases' => $cData, 'news' => $nData]);

            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    //删除轮播图
    public function sliderDel()
    {
        $id = input('param.id');

        //老照片
        $oldPhotoPath = $this->_sModel->getPhotoById($id);
        $oldPhotoPath = $oldPhotoPath['photo'];
        if(is_file(ROOT_PATH . 'public/uploads/' . $oldPhotoPath)){
            unlink(ROOT_PATH . 'public/uploads/' . $oldPhotoPath);
        }


        $flag = $this->_sModel->delSlider($id);

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /*访客列表*/
    public function ipLists()
    {
        $lModel = new \app\index\model\Log();
        if (request()->isAjax()) {

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['ip'] = ['like', '%' . $param['searchText'] . '%'];
            }


            $selectResult = $lModel->getIpListByWhere($where, $offset, $limit);

            //生成按钮
            foreach ($selectResult as $key => $vo) {

                $selectResult[$key]['last_time'] = date('Y-m-d H:i:s',$selectResult[$key]['last_time']);
                // $operate = ['删除' => "javascript:ipDel('" . $vo['id'] . "')"];
                $operate = [
                    '删除' => [
                        'href' => "javascript:ipDel('" . $vo['id'] . "')",
                        'auth' => 'data/ipdel'
                    ]
                ];
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $lModel->getAllIp($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }

        return $this->fetch();
    }

    //删除访客记录
    public function ipDel()
    {
        $id = input('param.id');

        $lModel = new \app\index\model\Log();

        $flag = $lModel->delIp($id);

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    //删除本月之前的所有访客记录
    public function ipMouthDel()
    {
        $beginThismonth = mktime(0,0,0,date('m'),1,date('Y'));
        $lModel = new \app\index\model\Log();
        $lModel::where(['last_time' => ['lt' , $beginThismonth]])->delete();
        return json(['code' => 1, 'data' => '', 'msg' => '删除成功']);
    }

    /**
     * 杂项设置
     * @return mixed|\think\response\Json
     */
    public function foot()
    {
        $footModel = new \app\common\model\Footer();
        try{
            if (request()->isAjax()) {

                $param = input('param.');
                $param = parseParams($param['data']);
                $arr = [
                    'title' => $param['title'],
                    'seo_keywords' => $param['seo_keywords'],
                    'seo_des' => $param['seo_des'],
                    'slogan' => $param['slogan'],
                    'email' => $param['email'],
                    'hr_email' => $param['hr_email'],
                    'tel' => $param['tel'],
                    'address' => $param['address'],
                    'ceo' => $param['ceo'],
                    'ceo_speak' => $param['ceo_speak'],
                    'company_hope' => $param['company_hope'],
                    'company_mission' => $param['company_mission'],
                    'company_belief' => $param['company_belief'],
                    'key_value' => $param['key_value'],
                    'update_time' => time()
                ];
                $flag = $footModel->updateInfo($arr);

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $data = $footModel->find(['id' => 1]);
            $this->assign('data', $data);
            return $this->fetch();

        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    public function calendar()
    {
        return $this->fetch();
    }

}
