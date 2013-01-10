#!/usr/bin/env php
<?php

include(__DIR__ . '/init.php');

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl, CURLOPT_URL, 'http://www.ptt.cc/hotboard.html');
curl_setopt($curl, CURLOPT_PROXY, 'proxy.hisoku.ronny.tw:80');
if (!$content = curl_exec($curl)) {
    exit;
}

$content = iconv('Big5', 'UTF-8//IGNORE', $content);
$content = preg_replace('/([\x{0fffe}-\x{10ffff}]+)/u','' , $content);

if (!preg_match('#\(本文約每小時更新，最後更新時間 ([^)]*)#', $content, $matches)) {
    throw new Exception('找不到時間');
}
if (!$time = strtotime($matches[1])) {
    throw new Exception('找不到時間');
}

$content = preg_replace('#<td[^>]*>#', '', $content);
$content = preg_replace('#<a [^>]*>#', '', $content);
$content = preg_replace('#</[^>]+>#', '', $content);
preg_match_all("#人氣：([0-9]*)\n([^\s]*)\n(.*)#m", ($content), $matches);

foreach ($matches[0] as $id => $data) {
    try {
        $board = strval($matches[2][$id]);
        $count = intval($matches[1][$id]);
        $name = strval($matches[3][$id]);

        RankData::insert(array(
            'time' => $time,
            'board' => $board,
            'count' => $count,
            'name' => $name,
        ));
    } catch (Pix_Table_DuplicateException $e) {
        RankData::search(array('time' => $time, 'board' => $board))->update(array('count' => $count, 'name' => $name));
    }
}

echo '完成: ' . date('c', $time) . "\n";
