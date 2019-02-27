<?php

namespace app\common\model;


use traits\model\SoftDelete;

class Factory extends Model
{
    use SoftDelete;

    protected $hidden = [
        "license_img",
        "factory_wx",
        "factory_contact",
        "factory_phone",
        "factory_province",
        "factory_city",
        "factory_district",
        "deliver_province",
        "deliver_city",
        "deliver_district",
        "create_by",
        "create_time",
        "update_by",
        "update_time",
        "delete_time",
    ];
}