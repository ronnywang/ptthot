<?php
include(__DIR__ . '/init.php');

Pix_Session::setCore('cookie', array('secret' => '60bf4e4009d422ab9322f1d4db48b02a'));
Pix_Controller::dispatch(__DIR__ . '/webdata');
