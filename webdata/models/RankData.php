<?php

class RankData extends Pix_Table
{
    public function init()
    {
	$this->_name = 'ptthot_rankdata';

	$this->_primary = array('board', 'time');

	$this->_columns['time'] = array('type' => 'int');
	$this->_columns['board'] = array('type' => 'varchar', 'size' => 32);
	$this->_columns['count'] = array('type' => 'int');

	$this->addIndex('time_rank', array('time', 'count', 'board'));
        $this->addIndex('board_rank', array('board', 'count', 'time'));
    }
}
