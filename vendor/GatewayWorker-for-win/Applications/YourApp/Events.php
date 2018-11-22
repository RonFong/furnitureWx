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
            'msg'   => $msg,
            'data'  => $data
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
//        try {
            $msg = json_decode($message, true);
            if (!$msg) {
                return;
            }
            switch ($msg['type']) {
                case 'bind':
                    $uid = $msg['userId'];
                    //用户登录后初始化
                    Gateway::bindUid($client_id, $uid);
                    Gateway::sendToUid($uid, self::result([
                        'type' => 'init',
                        'data' => '11111111'
                    ]));
                    break;
                    //管理员，加入管理员群组
                    if (strpos($uid, 'admin') !== false) {
                        Gateway::joinGroup($client_id, 'admin');
                    }
                    //保存登录记录
                    $messageList = self::init($uid);
                    Gateway::sendToUid($uid, self::result([
                        'type' => 'init',
                        'data' => $messageList
                    ]));
                    break;

                case 'toUser':
                    //发送消息给好友
                    $msg['data']['type'] = 'toUser';
                    $data = [
                        'type' => 'showMessage',
                        'data' => $msg['data']
                    ];
                    Gateway::sendToUid($msg['toid'], self::result($data));
                    break;
            }
            return;
//        } catch (\Exception $e) {
//            self::errorLog($e->getMessage());
//        }
    }

    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id)
    {
        try {
            $uid = Gateway::getUidByClientId($client_id);
            if (strpos($uid, 'admin') === false) {
                self::log($uid, 0);
                // 向管理组发送用户登出消息
                $data = [
                    'type' => 'logout',
                    'data' => ['user_id' => $uid]
                ];
                Gateway::sendToGroup('admin', self::result($data));
            }
        } catch (\Exception $e) {
            self::errorLog($e->getMessage());
        }
    }

    //用户登录后的消息统计
    private static function init($userId)
    {
        try {
            self::log($userId, 1);
            //与当前用户相关的消息， 过滤：用户已清除、后台已删除、后台已屏蔽的消息
            $messageList = self::$db->query("SELECT `from_id`, `to_id`, `message`, `type`, `read`, `send_time`, count(`from_id` + `to_id`) as `count` FROM (SELECT * FROM `websocket_message` WHERE ((`to_id` = $userId AND `to_clear` = 0) OR (`from_id` = $userId AND `from_clear` = 0)) AND `state` = 1 AND `delete_time` IS NULL ORDER BY `send_time` DESC limit 999999) as msg GROUP BY (`from_id` + `to_id`) ORDER BY `read`, `type` DESC, `send_time` DESC");
            if (!empty($messageList)) {
                $data['new_message_total'] = count($messageList) - array_sum(array_column($messageList, 'read'));
                foreach ($messageList as $k => $v) {
                    $id = $v['from_id'] != $userId ?: $v['to_id'];
                    $user = self::$db->select('user_name,avatar')->from('user')->where("id={$id}")->row();
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
     * 链接与断开  记录
     * @param $userId
     * @param $type
     */
    private static function log($userId, $type)
    {
        self::$db->insert('websocket_chained')
            ->cols(['user_id' => $userId, 'type' => $type, 'create_time' => time()])
            ->query();
    }

    private static function errorLog($msg)
    {
        self::$db->insert('error_log')
            ->cols(['url' => 'gatewaywork', 'msg' => $msg, 'time' => time()])
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
            $num = (int)($time / 31104000);
            return $num.'年前';
        }
        if ($time >= 2592000) { // N月前
            $num = (int)($time / 2592000);
            return $num.'月前';
        }
        if ($time >= 86400) { // N天前
            $num = (int)($time / 86400);
            return $num.'天前';
        }
        if ($time >= 3600) { // N小时前
            $num = (int)($time / 3600);
            return $num.'小时前';
        }
        if ($time >= 60) { // N分钟前
            $num = (int)($time / 60);
            return $num.'分钟前';
        }
        if ($time >= 30) { // N分钟前
            return "刚才";
        }
        return "刚刚";}
}
