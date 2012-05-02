#!/usr/bin/php
<?php
if (!$message = @http_get('http://www.ptt.cc/hotboard.html')) {
    exit;
}
$content = http_parse_message($message)->body;
$content = iconv('Big5', 'UTF-8//IGNORE', $content);
$content = preg_replace('/([\x{0fffe}-\x{10ffff}]+)/u','' , $content);

$site = 'hot.pttt.tw';
$message = http_post_fields('http://' . $site . '/api/addweb', array('content' => $content));
echo http_parse_message($message)->body;
