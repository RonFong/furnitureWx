<?php
namespace app\index\controller;

use app\common\controller\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
        $this->redirect('admin/index/index');
    }
}
