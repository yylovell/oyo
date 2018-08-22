<?php
namespace app\admin\controller;

use app\common\controller\Sys;
use think\Exception;

/**
 * 课程控制类
 *
 * Class News
 * @package app\admin\controller
 */
class Learncourse extends Base
{

    public $_lModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->_lModel = new \app\common\model\Learncourse();
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
            if (isset($param['searchText'])) {
                if ($param['searchText']['title']) {
                    $where['title'] = ['like', '%' . $param['searchText']['title'] . '%'];
                }
                if ((int)$param['searchText']['is_send'] !== 1000) {
                    $where['is_send'] = $param['searchText']['is_send'];
                }
            }


            $selectResult = $this->_lModel->getByWhere($where, $offset, $limit);

            //生成按钮
            foreach ($selectResult as $key => $vo) {

                // $operate = ['编辑' => url('edit', ['id' => $vo['id']]), '删除' => "javascript:del('" . $vo['id'] . "')"];
                $operate = [
                    '编辑' => [
                        'href' => url('edit', ['id' => $vo['id']]),
                        'auth' => 'learncourse/edit'
                    ],
                    '详情' => [
                        'href' => url('detail', ['id' => $vo['id']]),
                        'auth' => 'learncourse/detail'
                    ],
                    '删除' => [
                        'href' => "javascript:del('" . $vo['id'] . "')",
                        'auth' => 'learncourse/del'
                    ]
                ];
                $selectResult[$key]['is_send'] = onButton('learncourse/status', Sys::getIsSend($selectResult[$key]['is_send']), $selectResult[$key]['is_send'], "javascript:status('" . $vo['id'] . "' , '" . $selectResult[$key]['is_send'] . "')");

                $selectResult[$key]['course_type_name'] = Sys::getCourseType($selectResult[$key]['course_type']);
                $selectResult[$key]['operate'] = showOperate($operate);

            }

            $return['total'] = $this->_lModel->getAll($where);  //总数据
            $return['rows'] = $selectResult;

            return json($return);
        }
        $is_send_map = Sys::getIsSend();

        $this->assign('is_send_map', $is_send_map);
        return $this->fetch();
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

        $learnConfigModel = new \app\common\model\Learnconfig();
        $learnConfigData = $learnConfigModel->getByLearnId($id)->toArray();

        if (!$learnConfigData)
        {
            throw new Exception('课程时间配置不存在，启用失败，请添加配置');
        }

        if ($aData['course_type'] == Sys::COURSE_TYPE_VIP)
        {
            $courseGradeModel = new \app\common\model\Coursegrade();
            $courseGrade = $courseGradeModel->count();
            if ($courseGrade <= 0)
            {
                throw new Exception('课程等级不存在，启用失败，请添加课程等级');
            }
        }

        $param = [
            'id' => $id,
            'is_send' => Sys::NEWS_IS_SEND
        ];

        $flag = $this->_lModel->send($param);
        if ($flag['code'] > 0)
        {
            $this->logInfo('启用课程成功，课程ID：'. $id);
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

        $flag = $this->_lModel->send($param);
        if ($flag['code'] > 0)
        {
            $this->logWarn('停用课程成功，课程ID：'. $id);
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

            $learnData = $this->_lModel->getById($id)->toArray();
            if (!$learnData)
            {
                throw new Exception('课程不存在');
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

    //添加
    public function add()
    {
        //
        try {
            if (request()->isPost()) {
                $param = $_POST;

                $thumb = '';
                if ($param['photo']) {
                    $thumb = $this->_getThumb($param['photo']);
                }

                /*$time_area = $param['time_area'];
                $start_at = substr($time_area, 0, 10);
                $end_at = substr($time_area, 13);*/

                if ($param['course_type'] == Sys::COURSE_TYPE_LEARN)
                {
                    $param['grade_ids'] = '';
                }


                $arr = ['title' => $param['title'], 'course_type' => $param['course_type'], 'long' => $param['long'], 'forward' => $param['forward'], 'grade_ids' => $param['grade_ids'], 'des' => $param['des'], 'photo' => $param['photo'], 'photo_thumb' => $thumb,

                ];
                $arr['create_time'] = $arr['update_time'] = time();

                $flag = $this->_lModel->insert($arr);
                if ($flag['code'] > 0)
                {
                    $this->logInfo('添加课程成功，课程名称：'. $param['title']);
                }

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            /*找到顶级等级*/
            $gradeModel = new \app\common\model\Coursegrade();
            $grades = $gradeModel->where(['type_id' => 0, 'is_send' => Sys::COMMON_YES])->select();
            $this->assign('grades', $grades);

            $type_map = Sys::getCourseType();
            $this->assign('type_map', $type_map);
            return $this->fetch();

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
            return json(['code' => 0, 'data' => '', 'msg' => $info]);
        }

    }

    /**
     * 生成缩略图
     * @param string $photo_path 原图片路径
     * @return bool
     */
    public function _getThumb($photo_path)
    {
        $image = \think\Image::open(ROOT_PATH . 'public/uploads/' . $photo_path);
        $type = $image->type();
        $card_pic_thumb = md5($photo_path) . '.' . $type;
        $image->thumb(690, 434, \think\Image::THUMB_SCALING)->save(ROOT_PATH . 'public' . DS . 'uploads' . DS . 'thumb' . DS . $card_pic_thumb);

        return $card_pic_thumb;
    }

    //添加图片
    public function addPhoto()
    {
        try {
            if (!request()->file()) {
                throw new Exception('上传图片失败');
            }
            else {

                //判断上张图是否正在使用，无，则删除
                if (input('last_photo_path')) {

                    $last_photo_path = input('last_photo_path');

                    $count = $this->_lModel->getPhotoPath($last_photo_path);

                    if (!$count) {
                        if (is_file(ROOT_PATH . 'public/uploads/' . $last_photo_path)) {
                            unlink(ROOT_PATH . 'public/uploads/' . $last_photo_path);
                        }
                    }
                }

                $file = request()->file('upload_file');
                $info = $file->validate(['size' => 5242880, 'ext' => 'jpg,jpeg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
                if ($info) {
                    $photo_path = $info->getSaveName();

                    return json(['code' => 1, 'data' => $photo_path, 'msg' => '上传成功']);
                }
                else {
                    // 上传失败获取错误信息
                    $err_msg = $file->getError();

                    return json(['code' => 0, 'data' => '', 'msg' => $err_msg]);
                }

            }

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
            return json(['code' => 0, 'data' => '', 'msg' => $info]);
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
                $count = $this->_lModel->getPhotoPath($photo_path);

                if (!$count) {
                    if (is_file(ROOT_PATH . 'public/uploads/' . $photo_path)) {
                        unlink(ROOT_PATH . 'public/uploads/' . $photo_path);
                    }
                }
            }


            return json(['code' => 1, 'data' => '', 'msg' => '删除成功']);

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
            return json(['code' => 0, 'data' => '', 'msg' => $info]);
        }
    }

    //详情
    public function detail()
    {
        try {

            $id = input('param.id');
            $aData = $this->_lModel->getById($id);

            $type_map = Sys::getCourseType();
            $this->assign('type_map', $type_map);

            $this->assign(['data' => $aData]);

            return $this->fetch();

        } catch (Exception $e) {
            $info = $e->getMessage();
            $this->error($info);
        }

    }

    //编辑
    public function edit()
    {
        try {

            if (request()->isPost()) {
                $param = $_POST;

                //老照片
                $oldPhotoPath = $this->_lModel->getPhotoById($param['id']);
                $oldPhotoPath = $oldPhotoPath['photo'];
                if ($param['photo'] != $oldPhotoPath && $oldPhotoPath) {
                    if (is_file(ROOT_PATH . 'public/uploads/' . $oldPhotoPath)) {
                        unlink(ROOT_PATH . 'public/uploads/' . $oldPhotoPath);
                    }
                }

                /*$time_area = $param['time_area'];
                $start_at = substr($time_area, 0, 10);
                $end_at = substr($time_area, 13);*/

                if ($param['course_type'] == Sys::COURSE_TYPE_LEARN)
                {
                    $param['grade_ids'] = '';
                }

                $arr = ['id' => $param['id'], 'title' => $param['title'], 'course_type' => $param['course_type'], 'long' => $param['long'], 'forward' => $param['forward'], 'grade_ids' => $param['grade_ids'], 'des' => $param['des'], 'photo' => $param['photo'],

                ];
                $arr['update_time'] = time();
                $flag = $this->_lModel->edit($arr);

                if ($flag['code'] > 0)
                {
                    $this->logInfo('编辑课程成功，课程名称：'. $param['title']);
                }

                return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
            }

            $id = input('param.id');
            $aData = $this->_lModel->getById($id);
            $grade_ids = explode(',', $aData['grade_ids']);


            /*找到顶级等级*/
            $gradeModel = new \app\common\model\Coursegrade();
            $grades = $gradeModel->where(['type_id' => 0, 'is_send' => Sys::COMMON_YES])->select();
            foreach ($grades as &$grade)
            {
                if (in_array($grade['id'], $grade_ids))
                {
                    $grade['is_checked'] = 1;
                }
                else
                {
                    $grade['is_checked'] = 0;
                }
            }
            unset($grade);
            $this->assign('grades', $grades);

            $type_map = Sys::getCourseType();
            $this->assign('type_map', $type_map);

            $this->assign(['data' => $aData]);

            return $this->fetch();

        } catch (Exception $e) {
            $info = $e->getMessage();
            trace($info);
            return json(['code' => 0, 'data' => '', 'msg' => $info]);
        }

    }

    //删除
    public function del()
    {
        $id = input('param.id');
        $aData = $this->_lModel->where('id', $id)->find();
        //老照片
        $oldPhotoPath = $this->_lModel->getPhotoById($id);
        $oldPhotoPath = $oldPhotoPath['photo'];
        if (is_file(ROOT_PATH . 'public/uploads/' . $oldPhotoPath)) {
            unlink(ROOT_PATH . 'public/uploads/' . $oldPhotoPath);
        }


        $flag = $this->_lModel->del($id);
        if ($flag['code'] > 0)
        {
            $this->logWarn('删除课程成功，课程名称：'. $aData['title']);
        }

        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }
}