<?php
header('Content-Type:application/json');
$result = json_encode([
    'status' => 1,
    'url'    => 'https://www.7qiaoban.cn/static/img/user_icon/boy.png',
    'msg'    => '成功',
    'data'   => $_FILES,
]);
echo $result;
