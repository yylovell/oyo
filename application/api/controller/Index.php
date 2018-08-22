<?php
namespace app\api\controller;

use app\common\controller\Sys;
use think\Controller;
use think\Exception;
use think\Lang;

class Index extends Controller
{

    public function test(){
        header("Access-Control-Allow-Origin: http://localhost:8080");
        return json(['test'=>12]);
    }
}