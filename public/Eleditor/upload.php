<?php

$result = json_encode([
    'status' => 1,
    'url'    => 'http://img5.imgtn.bdimg.com/it/u=1126380040,3366465233&fm=27&gp=0.jpg',
    'msg'    => '成功',
    'data'   => $_FILES,
]);
return $result;
