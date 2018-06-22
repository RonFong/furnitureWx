<?php

$result = json_encode([
    'status' => 1,
    'url'    => '',
    'msg'    => '成功',
    'data'   => $_FILES,
]);
return $result;
