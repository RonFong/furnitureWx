<?php
header('Content-Type:application/json');
$result = json_encode([
    'status' => 1,
    'url'    => 'http://img.zcool.cn/community/0142135541fe180000019ae9b8cf86.jpg@1280w_1l_2o_100sh.png',
    'msg'    => '成功',
    'data'   => $_FILES,
]);
echo $result;
