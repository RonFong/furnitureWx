<?php
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------
// | CreateTime: 2018/7/16 
// +----------------------------------------------------------------------


namespace app\api\service;

/**
 * 产生关联
 * Class Relate
 * @package app\api\service
 */
class Relate
{
    /**
     * 当前操作的模型
     * @var object
     */
    protected $behaviorModel;

    public function __construct($behaviorModel)
    {
        try {
            $class =  '\\app\\common\\model\\' . $behaviorModel;
            $class = new \ReflectionClass($class);
            $this->behaviorModel = $class->newInstanceArgs();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function save($data)
    {
        $data['user_id'] = user_info('id');
        $method = $data['type'];
        return $this->$method($data);
    }

    /**
     * 增长
     * @param $data
     * @return string
     */
    protected function inc($data)
    {
        $data['create_date'] = date('Ymd', time());
        $data['create_time'] = time();
        $result = $this->behaviorModel->save($data);
        return $result ?? false;
    }

    /**
     * 减少
     * @param $data
     * @return bool
     */
    protected function dec($data)
    {
        unset($data['type']);
        $result = $this->behaviorModel->where($data)->delete();
        return $result ?? false;
    }
}