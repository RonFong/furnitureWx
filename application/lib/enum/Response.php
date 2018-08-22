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

    // 商店
    const SHOP_REGISTER_ERROR = ['code' => 400, 'errorCode' => 1001, 'msg' => '创建门店失败'];

    // 点赞、关注、收藏操作失败
    const RELATE_ERROR = ['code' => 400, 'errorCode' => 999, 'msg' => '操作失败'];

    // openid获取失败
    const GET_OPENID_ERROR = ['code' => 400, 'errorCode' => 999, 'msg' => 'openid获取失败'];

    //errorCode 值为  10000 时， 前端将重新请求 getToken 接口，此错误码不可更改和重用
    const TOKEN_NO_BRACE = ['code' => 403, 'errorCode' => 10000, 'msg' => '无效的userToken'];

    //order  排序参数错误
    const ORDER_ERROR = ['code' => 400, 'errorCode' => 999, 'msg' => 'order 参数错误'];

    //order  排序参数错误
    const COMMENT_FAIL = ['code' => 400, 'errorCode' => 1000, 'msg' => '评论失败'];

    //厂家用户ID错误
    const IS_NOT_FACTORY = ['code' => 400, 'errorCode' => 1000, 'msg' => '厂家用户ID错误'];

    //商家用户ID错误
    const IS_NOT_SHOP = ['code' => 400, 'errorCode' => 1000, 'msg' => '商家用户ID错误'];

    //未上传图片
    const IMG_FILE_CANT_EMPTY = ['code' => 400, 'errorCode' => 1000, 'msg' => '未上传图片'];

    //收藏的筛选条件错误
    const QUERY_ERROR = ['code' => 400, 'errorCode' => 1000, 'msg' => '筛选条件错误'];

}