<?php
include(__DIR__ . '/init.php');

Pix_Controller::addDispatcher(function($url){
    list(, $contoller, $action) = explode('/', $url, 3);
    switch ($contoller) {
    case 'board':
	return array('index', 'board');
    }
    return null;
});
Pix_Controller::dispatch(__DIR__ . '/webdata');
