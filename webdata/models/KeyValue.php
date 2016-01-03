<?php

class KeyValue extends Pix_Table
{
    public function init()
    {
        $this->_name = 'keyvalue';
        $this->_primary = 'key';

        $this->_columns['key'] = array('type' => 'varchar', 'size' => 32);
        $this->_columns['value'] = array('type' => 'text');
    }

    public static function set($key, $value)
    {
        try {
            KeyValue::insert(array(
                'key' => $key,
                'value' => $value,
            ));
        } catch (Pix_Table_DuplicateException $e) {
            KeyValue::find($key)->update(array('value' => $value));
        }
    }

    public static function get($key)
    {
        return KeyValue::find($key)->value;
    }
}
