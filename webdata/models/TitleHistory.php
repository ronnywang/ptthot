<?php

class TitleHistoryRow extends Pix_Table_Row
{
    public function postSave()
    {
        TitleHistory::clearCache($this->board);
    }
}

class TitleHistory extends Pix_Table
{
    public function init()
    {
        $this->_name = 'titlehistory';
        $this->_primary = array('board', 'time');
        $this->_rowClass = 'TitleHistoryRow';

        $this->_columns['board'] = array('type' => 'varchar', 'size' => 32);
        $this->_columns['time'] = array('type' => 'int');
        $this->_columns['title'] = array('type' => 'text');
    }

    protected static $_latest_cache = array();

    public static function clearCache($board)
    {
        unset(self::$_latest_cache[$board]);
    }

    public static function updateTitle($board, $time, $title)
    {
        $titlerow = self::getLatestTitle($board);
        if ($titlerow and $title == $titlerow->title) {
            return;
        }
        if ($time < $titlerow->time) {
            return;
        }
        TitleHistory::insert(array(
            'board' => $board,
            'time' => $time,
            'title' => $title,
        ));
    }

    public static function getLatestTitle($board)
    {
        if (!array_key_exists($board, self::$_latest_cache)) {
            self::$_latest_cache[$board] = TitleHistory::search(array('board' => $board))->max('time');
        }

        return self::$_latest_cache[$board];
    }
}
