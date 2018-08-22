<?php
/**
 * 生成操作按钮
 * @param array $operate 操作按钮数组
 */
function showOperate($operate = [])
{
    if(empty($operate)){
        return '';
    }
    $option = '';

    foreach($operate as $key=>$vo){
        switch ($key)
        {
            case '编辑':
                $i = 'fa-edit';
                $b = 'warning';
                break;
            case '详情':
                $i = 'fa-list-ol';
                $b = 'primary';
                break;
            case '删除':
                $i = 'fa-trash-o';
                $b = 'danger';
                break;
            case '备份':
                $i = 'fa-cloud-download';
                $b = 'primary';
                break;
            case '还原':
                $i = 'fa-cloud-upload';
                $b = 'info';
                break;
            case '分配权限':
                $i = 'fa-unlock';
                $b = 'success';
                break;
            case '添加固定课程':
                $i = 'fa-flag-checkered';
                $b = 'info';
                break;
            case '重置密码':
                $i = 'fa-lock';
                $b = 'success';
                break;
            case '队列':
                $i = 'fa-flag-checkered';
                $b = 'info';
                break;
            default:
                $i = 'fa-edit';
                $b = 'primary';
        }

        if(authCheck($vo['auth']))
        {
            $option .= '<a href="'.$vo['href'].'"><button type="button" class="btn btn-' . $b . ' btn-xs">'.'<i class="fa '.$i.'"></i> '.$key.'</button></a> ';
        }
    }

    return $option;
}

/**
 * 生成是否按钮
 * @param $rule
 * @param $name
 * @param $is_on
 * @param $href
 * @return string
 */
function onButton($rule, $name, $is_on, $href)
{
    $option = '';

    switch ($is_on)
    {
        case 0:
            $b = 'danger';
            break;
        case 1:
            $b = 'primary';
            break;
        default:
            $b = 'warning';
    }
    if(authCheck($rule))
    {
        $option .= '<a href="' . $href . '"><button type="button" class="btn btn-' . $b . ' btn-xs">' . $name . '</button></a>';
    }
    else
    {
        $option .= '<span>' . $name . '</span>';
    }
    return $option;
}

/**
 * 生成颜色背景文字
 * @param $name
 * @param $level
 * @param $style
 * @return string
 */
function color($name, $level, $style)
{
    $option = '';

    switch ($level)
    {
        case 1:
            $b = 'info';
            break;
        case 2:
            $b = 'danger';
            break;
        case 3:
            $b = 'success';
            break;
        case 4:
            $b = 'warning';
            break;
        case 5:
            $b = 'primary';
            break;
        default:
            $b = 'primary';
    }

    $option .= '<span class="btn btn-' . $b . ' btn-xs" style="'.$style.'">'.$name.'</span>';

    return $option;
}

/**
 * 权限检测
 * @param $rule
 */
function authCheck($rule)
{
    $control = explode('/', $rule)['0'];
    if(in_array($control, ['login', 'index'])){
        return true;
    }

    if(in_array($rule, session('action'))){
        return true;
    }

    return false;
}

/**
 * 将字符解析成数组
 * @param $str
 */
function parseParams($str)
{
    $arrParams = [];
    parse_str(html_entity_decode(urldecode($str)), $arrParams);
    return $arrParams;
}

/**
 * 子孙树 用于菜单整理
 * @param $param
 * @param int $pid
 */
function subTree($param, $pid = 0)
{
    static $res = [];
    foreach($param as $key=>$vo){

        if( $pid == $vo['pid'] ){
            $res[] = $vo;
            subTree($param, $vo['id']);
        }
    }

    return $res;
}

/**
 * 整理菜单主方法
 * @param $param
 * @return array
 */
function prepareMenu($param)
{
    $parent = []; //父类
    $child = [];  //子类

    foreach($param as $key=>$vo){

        if($vo['typeid'] == 0){
            $vo['href'] = '#';
            $parent[] = $vo;
        }else{
            $vo['href'] = url($vo['control_name'] .'/'. $vo['action_name']); //跳转地址
            $child[] = $vo;
        }
    }

    foreach($parent as $key=>$vo){
        foreach($child as $k=>$v){

            if($v['typeid'] == $vo['id']){
                $parent[$key]['child'][] = $v;
            }
        }
    }
    unset($child);

    return $parent;
}

/**
 * 解析备份sql文件
 * @param $file
 */
function analysisSql($file)
{
    // sql文件包含的sql语句数组
    $sqls = array ();
    $f = fopen ( $file, "rb" );
    // 创建表缓冲变量
    $create = '';
    while ( ! feof ( $f ) ) {
        // 读取每一行sql
        $line = fgets ( $f );
        // 如果包含空白行，则跳过
        if (trim ( $line ) == '') {
            continue;
        }
        // 如果结尾包含';'(即为一个完整的sql语句，这里是插入语句)，并且不包含'ENGINE='(即创建表的最后一句)，
        if (! preg_match ( '/;/', $line, $match ) || preg_match ( '/ENGINE=/', $line, $match )) {
            // 将本次sql语句与创建表sql连接存起来
            $create .= $line;
            // 如果包含了创建表的最后一句
            if (preg_match ( '/ENGINE=/', $create, $match )) {
                // 则将其合并到sql数组
                $sqls [] = $create;
                // 清空当前，准备下一个表的创建
                $create = '';
            }
            // 跳过本次
            continue;
        }

        $sqls [] = $line;
    }
    fclose ( $f );

    return $sqls;
}
