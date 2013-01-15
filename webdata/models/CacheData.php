<?php

class CacheData extends Pix_Table
{
    public function init()
    {
        $this->_name = 'cache_data';
        $this->_primary = array('board', 'period', 'time');

        $this->_columns['board'] = array('type' => 'varchar', 'size' => 32);
        // 0 - yearly, 1 - monthly, 2 - weekly, 3 - daily
        $this->_columns['period'] = array('type' => 'tinyint');
        $this->_columns['time'] = array('type' => 'int');
        $this->_columns['updated_at'] = array('type' => 'int');
        $this->_columns['data'] = array('type' => 'text');
    }

    public static function getData($board, $period, $time)
    {
        if (0 == $period) { // year: format: YYYY, 一天一筆最高的
            $max_time = mktime(0, 0, 0, 1, 1, $time + 1);
            $min_time = mktime(0, 0, 0, 1, 1, $time);
        } elseif (1 == $period) { // month: format: YYYYMM, 一天12筆
            list($year, $month) = sscanf($time, "%4d%2d");
            $min_time = mktime(0, 0, 0, $month, 1, $year);
            $max_time = strtotime('+1 month', $min_time);
        } elseif (3 == $period) { // daily: format: YYYYMMDD, 全部
            list($year, $month, $day) = sscanf($time, "%4d%2d%2d");
            $min_time = mktime(1, 1, 1, $month, $day, $year);
            $max_time = $min_time + 86400;
        }

        $last_update = RankData::search(array('board' => $board))->search("`time` < $max_time")->order("`time` DESC")->first()->time;
        if (!$last_update) {
            return array();
        }

        if ($data = CacheData::search(array("board" => $board, "period" => $period, "time" => $time, "updated_at" => $last_update))->first()) {
            return json_decode($data->data);
        }

        if (0 == $period) {
            $datas = array();
            for ($t = $min_time; $t < $max_time; $t += 86400) {
                $max_data = RankData::search(array('board' => $board))->search("`time` >= $t and `time` < $t + 86400")->max('count');
                if (!$max_data) {
                    continue;
                }
                $datas[] = array($max_data->time, $max_data->count, $max_data->name);
            }
        }

        try {
            CacheData::insert(array(
                'board' => $board,
                'period' => $period,
                'time' => $time,
                'updated_at' => $last_update,
                'data' => json_encode($datas, JSON_UNESCAPED_UNICODE),
            ));
        } catch (Pix_Table_DuplicateException $e) {
            CacheData::search(array(
                'board' => $board,
                'period' => $period,
                'time' => $time,
            ))->update(array(
                'updated_at' => $last_update,
                'data' => json_encode($datas, JSON_UNESCAPED_UNICODE),
            ));
        }
        return $datas;
    }
}
