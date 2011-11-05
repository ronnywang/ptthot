<?php

/**
 * Pix_Controller_Plugin_Http
 * 
 * @uses Pix
 * @uses _Controller_Plugin
 * @package Pix_Controller
 * @version $id$
 * @copyright 2003-2010 PIXNET
 * @author Shang-Rung Wang <srwang@pixnet.tw> 
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class Pix_Controller_Plugin_Http extends Pix_Controller_Plugin
{
    public function getFuncs()
    {
	return array('checkModified');
    }

    public function checkModified($controller, $options = array())
    {
	if (isset($options['etag']) and trim($_SERVER['HTTP_IF_NONE_MATCH']) == $options['etag']) {
	    $not_modified = true;
	} elseif (isset($options['last_modified_time']) and strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $options['last_modified_time']) {
	    $not_modified = true;
	} else {
	    $not_modified = false;
	}

	if ($options['last_modified_time']) {
	    header('Age: ' . (time() - $options['last_modified_time']));
	}

	$max_age = isset($options['max_age']) ? intval($options['max_age']) : 86400;
        // 有 Last-Modified-Time 的話, max-age 會按照 Last-Modified-Time 起算, 不是 max-age 之後 expire
        if (!$options['last_modified_time']) {
            header('Cache-Control: max-age=' . $max_age);
        }

	if ($not_modified) {
	    header("HTTP/1.1 304 Not Modified");
	    return $controller->noview();
	}

	if ($options['last_modified_time']) {
	    header('Last-Modified: ' . date("r", $options['last_modified_time']));
	    header('Expires: ' . date("r", time() + $max_age));
	}
	if ($options['etag']) {
	    header('Etag: ' . $options['etag']);
	}
        // 不要設定 Pragma
        header('Pragma: ');
    }
}
