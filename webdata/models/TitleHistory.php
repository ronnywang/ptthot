<?php

class TitleHistory extends Pix_Table
{
    public function init()
    {
        $this->_name = 'titlehistory';
        $this->_primary = array('board', 'time');

        $this->_columns['board'] = array('type' => 'varchar', 'size' => 32);
        $this->_columns['time'] = array('type' => 'int');
        $this->_columns['title'] = array('type' => 'text');
    }
}
