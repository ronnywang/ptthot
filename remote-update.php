#!/usr/bin/env php
<?php

include(__DIR__ . '/init.php');
include(__DIR__ . '/Big52003.php');

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl, CURLOPT_TIMEOUT, 20);
curl_setopt($curl, CURLOPT_HTTPHEADER, arraY('User-Agent: Chrome'));
curl_setopt($curl, CURLOPT_URL, 'https://www.ptt.cc/bbs/hotboards.html');
if (!$content = curl_exec($curl)) {
    exit;
}

$curl = curl_init("https://ptthot.ronny.tw/update/remote");
curl_setopt($curl, CURLOPT_POSTFIELDS, "REMOTE_KEY=" . urlencode(getenv('REMOTE_KEY')) . '&content=' . urlencode($content));
curl_exec($curl);
