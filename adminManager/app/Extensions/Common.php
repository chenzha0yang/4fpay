<?php
/**
 * 公共函数
 * 只负责逻辑处理
 * 不链接数据库
 */

namespace App\Extensions;

trait Common
{
    /**
     * 返回提示信息
     *
     * @param $code
     * @param string $file
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    public function translateInfo($code, $file = '')
    {
        if (empty($file)) {
            if (is_int($code)) {
                if ($code < 2000) {
                    $file = 'error';
                } else {
                    $file = 'success';
                }
                if ($code === Code::SUCCESS) {
                    $file = 'success';
                }
            }
        }

        return trans("{$file}.{$code}");
    }

    /**
     * 获取分页查询条件
     *
     * @param array $page
     * @return array
     */
    public function getPageOffset(array $page)
    {
        $page['page']   = (int)$page['page'];
        $page['limit']  = (int)$page['limit'];
        $page['offset'] = ($page['page'] - 1) * $page['limit'];
        return $page;
    }

    /**
     * 获取查询时间-- 默认查询当天
     *
     * @param array $date
     * @param int $day
     * @return array
     */
    public static function getSelectDayTime(array $date = [], int $day = 1)
    {
        if (!empty($date['startDate'])) {
            return [
                (str_replace('+', ' ', $date['startDate'])),
                (str_replace('+', ' ', $date['endDate']))
            ];
        } else {
            return [
                date('Y-m-d 00:00:00'),
                date('Y-m-d H:i:s')
            ];
        }
    }


    /**
     * 验证ip是否符合规则
     * @param string $ipList
     * @return bool
     */
    public static function checkIpList(string $ipList = '')
    {
        //校验ip规则
        if (!empty($ipList)) {
            $list = explode(',', $ipList);
            $i    = 0;
            foreach ($list as $item) {
                if (filter_var($item, FILTER_VALIDATE_IP)) {
                    $i = $i + 1;
                }
            }
            if ($i === count($list)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 按时区格式化时间戳为标准时间
     *
     * @param int|object $time
     * @param string $type
     * @return false|string
     */
    public static function formatTime($time, $type = 'PRC')
    {
        if (is_object($time)) {
            $time = strtotime((string)$time);
        }
        if (strtoupper($type) === 'PRC') {
            return date("Y-m-d H:i:s", $time + 43200);
        }

        return date("Y-m-d H:i:s", $time);
    }

    /**
     * 对象转数组
     *
     * @param $object
     * @return mixed
     */
    public static function objectToArray($object)
    {
        $array = [];
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        } else {
            $array = $object;
        }
        return $array;
    }
}