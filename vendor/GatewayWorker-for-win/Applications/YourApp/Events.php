<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;

use \GatewayWorker\Lib\Db;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    protected static $db;

    protected static function result($data = [], $msg = 'success', $state = 1)
    {
        $result = [
            'state' => $state,
            'msg' => $msg,
            'data' => $data
        ];
        return json_encode($result);
    }

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
        try {
            self::$db = Db::instance('dataBase');
            // 向当前client_id发送数据
            Gateway::sendToClient($client_id, self::result(['type' => 'bind']));
        } catch (\Exception $e) {
            self::errorLog($e->getMessage());
        }
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($client_id, $message)
    {
        try {
            $msg = json_decode($message, true);
            if (!$msg) {
                return;
            }
            switch ($msg['type']) {
                case 'bind':
                    if (empty($msg['userId'])) {
                        Gateway::sendToClient($client_id, self::result([], 'bind: userId 不能为空', 0));
                        return;
                    }
                    $userId = $msg['userId'];

                    //用户登录后初始化
                    Gateway::bindUid($client_id, $userId);
                    $_SESSION[$client_id] = $userId;

                    if (strpos($userId, 'service') !== false || self::isServiceAccount($userId)) {
                        //后台管理员 和 客服账号 加入客服群组
                        Gateway::joinGroup($client_id, 'service');
                    }

                    //获取消息列表
                    static::getMessageList($userId);

                    // 向客服组发送用户登录消息
                    $data = [
                        'type' => 'userLogin',
                        'data' => ['user_id' => $userId]
                    ];
                    Gateway::sendToGroup('service', self::result($data), $client_id);
                    break;

                case 'postMessage':
                    //发送消息给用户
                    $fromId = $_SESSION[$client_id];
                    if (empty($msg['toId']) || empty($msg['message'])) {
                        Gateway::sendToClient($client_id, self::result([], 'postMessage: toId 和 message 不能为空', 0));
                        return;
                    }
                    $data = [
                        'type' => 'receiveMessage',
                        'from_id' => $fromId,
                        'message' => $msg['message'],
                    ];

                    $messageType = 1;
                    //和客服相关的，都发到客服群
                    if (self::isServiceAccount($msg['toId']) || self::isServiceAccount($fromId)) {
                        //发到客服群组
                        $data['toId'] = $msg['toId'];   //发给哪个客服的
                        Gateway::sendToGroup('service', self::result($data), $client_id);
                        $messageType = 2;
                    }

                    //发给一般用户
                    if (!self::isServiceAccount($msg['toId'])) {
                        Gateway::sendToUid($msg['toId'], self::result($data));
                    }
                    //保存消息
                    self::saveMessage($fromId, $msg['toId'], $msg['message'], $messageType);
                    //获取更新后的消息列表
                    static::getMessageList($msg['toId']);
                    static::getMessageList($fromId);
                    break;

                case 'messageBeenReadOne':
                    //设置与某个用户的聊天消息为已读
                    if (empty($msg['message_user_id'])) {
                        Gateway::sendToClient($client_id, self::result([], 'messageBeenReadOne: message_user_id 不能为空', 0));
                        return;
                    }
                    $userId = $_SESSION[$client_id];
                    self::messageBeenRead($userId, $msg['message_user_id']);
                    static::getMessageList($userId);
                    break;

                case 'messageBeenReadAll':
                    //设置全部聊天消息为已读
                    $userId = $_SESSION[$client_id];
                    self::messageBeenRead($userId);
                    static::getMessageList($userId);
                    break;

                case 'clearMessageOne':
                    //清空与某个用户的聊天消息
                    $userId = $_SESSION[$client_id];
                    self::clearMessage($userId, $msg['message_user_id']);
                    static::getMessageList($userId);
                    break;

                case 'clearMessageAll':
                    //清空全部聊天消息
                    $userId = $_SESSION[$client_id];
                    self::clearMessage($userId);
                    static::getMessageList($userId);
                    break;

                default:
                    Gateway::sendToClient($client_id, self::result([], 'type 值错误', 0));
                    return;
                    break;
            }
        } catch (\Exception $e) {
            self::errorLog($e->getMessage());
        }
        return;
    }

    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id)
    {
        try {
            $userId = $_SESSION[$client_id];
            if (strpos($userId, 'service') === false) {
                self::log($userId, 0);
                // 向客服组发送用户登出消息
                $data = [
                    'type' => 'userLogout',
                    'data' => ['user_id' => $userId]
                ];
                Gateway::sendToGroup('service', self::result($data));
            }
        } catch (\Exception $e) {
            self::errorLog($e->getMessage());
        }
    }

    //用户登录后的消息统计
    private static function messageList($userId)
    {
        try {
            self::log($userId, 1);
            //与当前用户相关的消息， 过滤：用户已清除、后台已删除、后台已屏蔽的消息
            $messageList = self::$db->query("SELECT `from_id`, `to_id`, `message`, `type`, `read`, `send_time` FROM (SELECT * FROM `websocket_message` WHERE ((`to_id` = $userId AND `to_clear` = 0) OR (`from_id` = $userId AND `from_clear` = 0)) AND `state` = 1 AND `delete_time` IS NULL ORDER BY `send_time` DESC limit 999999) as msg GROUP BY (`from_id` + `to_id`) ORDER BY `read` DESC, `type` DESC, `send_time` DESC");
            if (!empty($messageList)) {
                $data['new_message_total'] = count($messageList) - array_sum(array_column($messageList, 'read'));
                foreach ($messageList as $k => $v) {
                    $id = $v['from_id'] !== $userId ? $v['from_id'] : $v['to_id'];
                    $user = self::$db->select('id,user_name,avatar')->from('user')->where("id={$id}")->row();
                    $userMessage = self::$db->query("SELECT count(`id`) as num FROM `websocket_message` WHERE from_id = $id and to_id = $userId and `read` = 0 and state = 1 and delete_time is null");
                    $messageList[$k]['count'] = $userMessage[0]['num'];   //未读消息数
                    $messageList[$k]['user_id'] = $user['id'];
                    $messageList[$k]['user_name'] = $user['user_name'];
                    $messageList[$k]['avatar'] = $user['avatar'];
                    $messageList[$k]['send_time'] = self::timeFormatForHumans($v['send_time']);
                    unset($messageList[$k]['from_id'], $messageList[$k]['to_id']);
                }
                $data['message_list'] = $messageList;
                return $data;
            }
            return ['unread_count' => 0, 'list' => []];
        } catch (\Exception $e) {
            self::errorLog($e->getMessage());
        }
    }

    /**
     * 保存消息到数据库
     * @param $fromId
     * @param $toId
     * @param $message
     * @param $type
     */
    private static function saveMessage($fromId, $toId, $message, $type)
    {
        $saveData = [
            'from_id' => $fromId,
            'to_id' => $toId,
            'message' => $message,
            'type' => $type,
            'send_time' => time()
        ];
        self::$db->insert('websocket_message')->cols($saveData)->query();
    }

    /**
     * 判断一个用户是否为客服账号
     * @param $userId
     * @return bool
     */
    private static function isServiceAccount($userId)
    {
        $user = self::$db->select('is_service_account')->from('user')->where("id={$userId}")->row();
        return $user['is_service_account'] == 1 ? true : false;
    }

    /**
     * 获取消息列表
     * @param $userId
     */
    private static function getMessageList($userId)
    {
        $messageList = self::messageList($userId);
        $messageList['type'] = 'messageList';
        Gateway::sendToUid($userId, self::result($messageList));
    }

    /**
     * 设置聊天消息为已读
     * @param $fromId
     * @param $toId
     */
    private static function messageBeenRead($toId, $fromId = 0)
    {
        if ($fromId == 0) {
            //  type <> 3 不可同时操作系统消息和用户消息
            $map = "to_id = $toId and state = 1 and type <> 3";
        } else {
            $map = "to_id = $toId and from_id = $fromId and state = 1";
        }
        self::$db->update('websocket_message')
            ->where($map)
            ->cols(['read' => 1, 'read_time' => time()])
            ->query();
    }

    /**
     * 清空聊天记录
     * @param $currentUser
     * @param int $otherUser
     */
    private static function clearMessage($currentUser, $otherUser = 0)
    {
        if ($otherUser == 0) {
            //   type <> 3  不可同时操作系统消息和用户消息
            self::$db->update('websocket_message')->where("from_id = $currentUser and type <> 3")->cols(['from_clear' => 1])->query();
            self::$db->update('websocket_message')->where("to_id = $currentUser and type <> 3")->cols(['to_clear' => 1])->query();
        } else {
            self::$db->update('websocket_message')->where("from_id = $currentUser and to_id = $otherUser")->cols(['from_clear' => 1])->query();
            self::$db->update('websocket_message')->where("to_id = $currentUser and from_id = $otherUser")->cols(['to_clear' => 1])->query();
        }
    }


    /**
     * 登录登出 记录
     * @param $userId
     * @param $type
     */
    private static function log($userId, $type)
    {
        self::$db->insert('websocket_chained')
            ->cols(['user_id' => $userId, 'type' => $type, 'create_time' => time()])
            ->query();
    }

    /**
     * 系统错误
     * @param $msg
     */
    private static function errorLog($msg)
    {
        self::$db->insert('error_log')
            ->cols(['url' => 'gatewaywork', 'msg' => $msg, 'time' => date('Y-m-d H:i:s', time())])
            ->query();
    }

    /**
     * 格式化时间
     * @param $agoTime
     * @return string
     */
    private static function timeFormatForHumans($agoTime)
    {
        $agoTime = (int)$agoTime;
        // 计算出当前日期时间到之前的日期时间的毫秒数，以便进行下一步的计算
        $time = time() - $agoTime;

        if ($time >= 31104000) { // N年前
            return date('Y-m-d', $time);
        }
        if ($time >= 2592000) { // N月前
            $num = (int)($time / 2592000);
            return $num . '月前';
        }
        if ($time >= 86400) { // N天前
            $num = (int)($time / 86400);
            return $num . '天前 ' . date('H:s', $time);
        }
        if ($time >= 3600) { // N小时前
            $num = (int)($time / 3600);
            return $num . '小时前';
        }
        if ($time >= 60) { // N分钟前
            $num = (int)($time / 60);
            return $num . '分钟前';
        }
        if ($time >= 30) { // N分钟前
            return "刚才";
        }
        return "刚刚";
    }
}
