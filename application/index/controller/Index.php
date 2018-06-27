<?php
namespace app\index\controller;

use app\common\controller\Controller;

class Index extends Controller
{
    public function index()
    {
        $this->redirect('admin/index/index');
    }
}
