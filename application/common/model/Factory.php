<?php

namespace app\common\model;


use traits\model\SoftDelete;

class Factory extends Model
{
    use SoftDelete;

    protected $hidden = [
        "create_by",
        "create_time",
        "update_by",
        "update_time",
        "delete_time",
    ];
}