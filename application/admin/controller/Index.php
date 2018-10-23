<?php
namespace app\admin\controller;

use app\common\controller\Sys;
use app\admin\model\UserModel;
use think\Exception;

/**
 * 首页控制类
 *
 * Class Index
 * @package app\admin\controller
 */
class Index extends Base
{
    public function index()
    {
        $is_music = 0;

        $user = new UserModel();
        $userData = $user->where(['username' => session('username')])->find();
        if ($userData['typeid'] == 5 || $userData['typeid'] == 6)
        {
            $is_music = 1;
        }
        $this->assign('is_music', $is_music);

        // 查找新闻资讯分类
        /*if (cookie('news_cat'))
        {
            $news_cat = cookie('news_cat');
        }
        else
        {
            $url = "https://way.jd.com/jisuapi/channel?appkey=" . config('jd_appkey');
            $result = curl_get($url);
            $wxResult = json_decode($result, true);
            if ($wxResult && $wxResult['code'] == '10000')
            {
                $news_cat = $wxResult['result']['result'];
                cookie('news_cat', $news_cat, 3600*24);
            }
            else
            {
                $news_cat = [];
            }
        }

        $this->assign('news_cat', $news_cat);*/

        return $this->fetch('/index');
    }

    /**获取新闻
     * @return array
     */
    public function getNews()
    {
        try
        {
            if (request()->isPost())
            {
                $cat_name = input('channel', '头条');
                $key = input('cat_key', 'top');
                if (!$cat_name)
                {
                    throw new Exception('数据缺失');
                }

                if (cache('news_now_' . $key))
                {
                    $news_now = cache('news_now_' . $key);
                }
                else
                {
                    // 查找新闻资讯
                    $url = "http://v.juhe.cn/toutiao/index?type=$key&key=" . config('jh_new_appkey');
                    //$url = "https://way.jd.com/jisuapi/get?channel=$cat_name&num=10&start=0&appkey=" . config('jd_appkey');
                    $result = curl_get($url);
                    $wxResult = json_decode($result, true);
                    if ($wxResult && $wxResult['error_code'] == 0)
                    {
                        $news_now = [];
                        foreach ($wxResult['result']['data'] as $k =>$item)
                        {
                            $news_now[$k]['pic'] = $item['thumbnail_pic_s'];
                            $news_now[$k]['src'] = $item['author_name'];
                            $news_now[$k]['title'] = $item['title'];
                            $news_now[$k]['time'] = $item['date'];
                            $news_now[$k]['weburl'] = $item['url'];
                        }
                        cache('news_now_' . $key, $news_now, 300);
                    }
                    else
                    {
                        $news_now = [];
                    }
                }

                $this->assign('news_now', $news_now);
                return ['code' => 1, 'data' => $this->fetch(), 'msg' => '获取成功'];
            }

        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**获取影视
     * @return array
     */
    public function getFilms()
    {
        try
        {
            if (request()->isPost())
            {
                $city_name = input('city', '');

                if (!$city_name)
                {
                    throw new Exception('数据缺失');
                }

                if (cookie('film_date') && cookie('film_date') == date('Y-m-d', time()) && cookie('city') && cookie('city') == $city_name)
                {
                    $films = cache('films');
                }
                else
                {
                    // 查找影视资讯
                    $key = config('jh_appkey');
                    $url = "http://op.juhe.cn/onebox/movie/pmovie?key=$key&city=$city_name";
                    $result = curl_get($url);
                    $wxResult = json_decode($result, true);

                    if ($wxResult && $wxResult['error_code'] == 0)
                    {
                        $films = $wxResult['result']['data'];
                        cookie('film_date', date('Y-m-d', time()), 3600*24);
                        cookie('city', $city_name, 3600*24);
                        cache('films', $films, 3600*24);
                    }
                    else
                    {
                        $films = [];
                    }
                }

                $this->assign('films', $films);
                return ['code' => 1, 'data' => $this->fetch(), 'msg' => '获取成功'];
            }

        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 获取最新消息
     * @return array
     */
    public function getOpLists()
    {
        try
        {
            if (request()->isPost())
            {
                // 未读消息
                $opModel = new \app\common\model\Operatelog();
                $where['delete_at'] = ['eq', '0000-00-00 00:00:00'];
                $where['is_read'] = 0;

                $opList = $opModel
                    ->alias('o')
                    ->join('oyo_user u', 'u.id = o.admin_id')
                    ->where($where)
                    ->field('o.*, u.username')
                    ->order('o.time desc')
                    ->select();
                //生成按钮
                foreach ($opList as $key => $vo) {
                    $opList[$key]['level'] = color(Sys::getLogLevelName($opList[$key]['level']), $opList[$key]['level'], 'width:30px;height:30px;border-radius:50%;text-align:center;line-height:30px;padding:0;margin:0;');

                }
                $this->assign('op_lists', $opList);
                $this->assign('log_num', count($opList));
                return ['code' => 1, 'data' => $this->fetch(), 'msg' => '获取成功'];
            }

        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    // 标记为已读
    public function read()
    {
        try
        {
            $id = input('id', 0);
            if (!$id)
            {
                throw new Exception('数据错误');
            }
            $opModel = new \app\common\model\Operatelog();
            $flag = $opModel->readOne($id);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        catch (Exception $e)
        {
            return json(['code' => 0, 'data' => '', 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 后台默认首页1
     * @return mixed
     */
    public function indexPage()
    {
        /*$info = array(
            'PCTYPE'=>PHP_OS,
            'RUNTYPE'=>$_SERVER["SERVER_SOFTWARE"],
            'ONLOAD'=>ini_get('upload_max_filesize'),
            'ThinkPHPTYE'=>THINK_VERSION,
        );*/

        /*总访问人数*/
        $lModel = new \app\index\model\Log();
        $aCount = $lModel->getCount();
        $this->assign('ip_count', $aCount);

        /*总共案例个数*/
        $cModel = new \app\index\model\Cases();
        $cCount = $cModel->count();
        $this->assign('case_count', $cCount);

        /*新闻总数*/
        $nModel = new \app\index\model\News();
        $nCount = $nModel->count();
        $this->assign('new_count', $nCount);

        return $this->fetch('index');
    }

    /*获取一周访问数据*/
    public function getIpBar()
    {
        try {

            if (cache('ip_log') && cache('ip_log')['update_at'] == date('Y-m-d', time()))
            {
                $aData = cache('ip_log')['data'];
            }
            else
            {
                $lModel = new \app\index\model\Log();
                /*最近一周访问人数*/
                $today_start_time = strtotime(date('Y-m-d'));//今天凌晨时间戳
                $time1 = date('Y-m-d', $today_start_time - 86400 * 6);
                $time2 = date('Y-m-d', $today_start_time - 86400 * 5);
                $time3 = date('Y-m-d', $today_start_time - 86400 * 4);
                $time4 = date('Y-m-d', $today_start_time - 86400 * 3);
                $time5 = date('Y-m-d', $today_start_time - 86400 * 2);
                $time6 = date('Y-m-d', $today_start_time - 86400);
                $time7 = date('Y-m-d');

                $aData = [];
                $aData['day'] = [(substr($time1, 5)), (substr($time2, 5)), (substr($time3, 5)), (substr($time4, 5)), (substr($time5, 5)), (substr($time6, 5)), (substr($time7, 5))];
                $aData['all_count'] = [0, 0, 0, 0, 0, 0, 0];

                $dayData = $lModel->where(['last_time' => ['egt', $today_start_time - 86400 * 6]])->field('last_time')->select();
                foreach ($dayData as $v) {
                    $time = date('Y-m-d', $v['last_time']);
                    switch ($time) {
                        case $time1:
                            $aData['all_count'][0] += 1;
                            break;
                        case $time2:
                            $aData['all_count'][1] += 1;
                            break;
                        case $time3:
                            $aData['all_count'][2] += 1;
                            break;
                        case $time4:
                            $aData['all_count'][3] += 1;
                            break;
                        case $time5:
                            $aData['all_count'][4] += 1;
                            break;
                        case $time6:
                            $aData['all_count'][5] += 1;
                            break;
                        case $time7:
                            $aData['all_count'][6] += 1;
                            break;
                        /*default:
                            $aData['all_count'][6] += 1;*/
                    }
                }
                cache('ip_log', ['data'=> $aData, 'update_at'=> date('Y-m-d', time())], 3600*24);
            }

            return $aData;


        } catch (Exception $e) {
            trace($e->getMessage());
        }
    }

    /*获取案例分类数量*/
    public function getCasePie()
    {
        try {
            if (cache('ha'))
            {
                $ha = cache('ha');
            }
            else
            {
                $cModel = new \app\index\model\Cases();
                $ccModel = new \app\common\model\Casecat();

                $aData = $ccModel->getCaseCat();
                $type_name_map = [];
                foreach ($aData as $v){
                    $type_name_map[$v['id']] = $v;
                }


                $ha = ['name' => [], 'count' => []];
                $aaData = $cModel->field('type, count(type) as type_count')->group('type')->order('type')->select();
                foreach ($aaData as $k => $v) {
                    $ha['name'][] = $type_name_map[$v['type']]['name'];
                    $ha['count'][$k]['name'] = $type_name_map[$v['type']]['name'];
                    $ha['count'][$k]['value'] = $v['type_count'];
                }

                cache('ha', $ha, 3600*24);
            }

            return $ha;

        } catch (Exception $e) {
            trace($e->getMessage());
        }
    }

    /**
     * 音乐老师首页
     * @return mixed
     */
    public function indexMusic()
    {
        /*学员人数*/
        $sModel = new \app\common\model\Students();
        $aCount = $sModel->count();
        $this->assign('student', $aCount);

        /*体验人次*/
        $lsModel = new \app\common\model\Learnstudent();
        $lsCount = $lsModel->count();
        $this->assign('learn', $lsCount);

        return $this->fetch('index2');
    }

    /**
     * 会员课
     * @return array
     */
    public function getVip()
    {
        $vModel = new \app\common\model\Vipstudent();
        try
        {
            if (request()->isPost())
            {
                $day = input('day', '');
                if (!$day)
                {
                    $day = date('Y-m-d', time());
                }
                $Min = $day . ' ' . '00:00:00';
                $Max = $day . ' ' . '23:59:59';

                $aData = $vModel
                    ->alias('v')
                    ->join('oyo_learn_course lc', 'lc.id = v.learn_id')
                    ->join('oyo_course_grade c', 'c.id = v.grade_id')
                    ->join('oyo_students s', 's.id = v.student_id')
                    ->where(['start_at' =>['>=', $Min], 'end_at' => ['<=', $Max]])
                    ->field('v.grade_id, v.start_at, v.end_at, c.name as grade_name')
                    ->order('start_at')
                    ->select();

                $aDataTime = [];
                foreach ($aData as $item)
                {
                    $day = substr($item['start_at'], 0, 10);
                    $start_at = substr($item['start_at'], 11, 5);
                    $end_at = substr($item['end_at'], 11, 5);

                    $aDataTime[$day . ' ' . $start_at . ' 至 '. $end_at][] = $item;
                }

                $aDataGrade = [];
                foreach ($aDataTime as $k=>$v)
                {
                    foreach ($v as $item)
                    {
                        $aDataGrade[$k][$item['grade_id']][] = $item;
                    }
                }

                /*return json_encode($aDataGrade, JSON_UNESCAPED_UNICODE);*/
                $this->assign('vipcourse', $aDataGrade);
                return ['code' => 1, 'data' => $this->fetch(), 'msg' => '获取成功'];
            }

        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 体验课
     * @return array
     */
    public function getLearn()
    {
        $lsModel = new \app\common\model\Learnstudent();
        try
        {
            if (request()->isPost())
            {
                $day = input('day', '');
                if (!$day)
                {
                    $day = date('Y-m-d', time());
                }
                $Min = $day . ' ' . '00:00:00';
                $Max = $day . ' ' . '23:59:59';

                $lData = $lsModel
                    ->where(['start_at' =>['>=', $Min], 'end_at' => ['<=', $Max]])
                    ->order('start_at')
                    ->select();
                $lDataTime = [];
                foreach ($lData as $item)
                {
                    $day = substr($item['start_at'], 0, 10);
                    $start_at = substr($item['start_at'], 11, 5);
                    $end_at = substr($item['end_at'], 11, 5);
                    $lDataTime[$day . ' ' . $start_at . ' 至 '. $end_at][] = $item;
                }
                $this->assign('learncourse', $lDataTime);
                return ['code' => 1, 'data' => $this->fetch(), 'msg' => '获取成功'];
            }

        }
        catch (Exception $e)
        {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

}
