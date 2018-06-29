<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/6/15 
// +----------------------------------------------------------------------

namespace app\lib\enum;

/**
 * 错误代码
 * 参考豆瓣API
 * Class Response
 * @package app\lib\enum
 */
class Response
{
//HTTP 状态码
//200	OK	请求成功
//201	CREATED	创建成功
//202	ACCEPTED	更新成功
//400	BAD REQUEST	请求的地址不存在或者包含不支持的参数
//401	UNAUTHORIZED	未授权
//403	FORBIDDEN	被禁止访问
//404	NOT FOUND	请求的资源不存在
//500	INTERNAL SERVER ERROR	内部错误


//错误码
//999	unknow_v2_error	未知错误	400
//1000	missing_args	参数错误	400
//1001	uri_not_found	资源不存在	404
//1002	image_too_large	上传的图片太大	400
//1003	need_permission	需要权限	403
//1004	has_ban_word	输入有违禁词	400
//1005	input_too_short	输入为空，或者输入字数不够	400
//1006	target_not_fount	相关的对象不存在，比如回复帖子时，发现小组被删掉了	400
//1007	need_captcha	需要验证码，验证码有误	403
//1008	image_unknow	不支持的图片格式	400
//1009	image_wrong_format	照片格式有误(仅支持JPG,JPEG,GIF,PNG或BMP)	400
//1010	image_wrong_ck	访问私有图片ck验证错误	403
//1011	image_ck_expired	访问私有图片ck过期	403
//1012	title_missing	题目为空	400
//1013	desc_missing	描述为空	400

    //用户模块
    const USER_NO_EXISTS = ['code' => 404, 'errorCode' => 1001, 'msg' => '此用户不存在'];
    const USER_CREATE_ERROR = ['code' => 400, 'errorCode' => 999, 'msg' => '用户注册失败'];
    const USER_UPDATE_ERROR = ['code' => 400, 'errorCode' => 999, 'msg' => '用户数据修改失败'];
    const USER_DELETE_ERROR = ['code' => 400, 'errorCode' => 999, 'msg' => '用户数据删除失败'];
    const USERS_EMPTY = ['code' => 404, 'errorCode' => 1001, 'msg' => '未查询到用户数据'];

    //音乐
    const QUERY_CANT_EMPTY = ['code' => 400, 'errorCode' => 1001, 'msg' => '搜索条件不能为空'];

}