<?php

class WebBackup extends Pix_Table
{
    public function init()
    {
	$this->_name = 'ptthot_webbackup';

	$this->_primary = 'time';

	$this->_columns['time'] = array('type' => 'int');
	$this->_columns['content'] = array('type' => 'text');
	$this->_columns['parsed_at'] = array('type' => 'int', 'default' => 0);
    }
}
