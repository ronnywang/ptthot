#!/usr/bin/env php
<?php

include(__DIR__ . '/init.php');
include(__DIR__ . '/Big52003.php');

$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($curl, CURLOPT_TIMEOUT, 20);
curl_setopt($curl, CURLOPT_URL, 'https://www.ptt.cc/hotboard.html');
if (!$content = curl_exec($curl)) {
    exit;
}

$content = Big52003::iconv($content);
//$content = preg_replace('/([\x{0fffe}-\x{10ffff}]+)/u','' , $content);

if (!preg_match('#\(本文約每小時更新，最後更新時間 ([^)]*)#', $content, $matches)) {
    throw new Exception('找不到時間');
}
if (!$time = strtotime($matches[1])) {
    throw new Exception('找不到時間: ' . $matches[1]);
}

$content = preg_replace('#<td[^>]*>#', '', $content);
$content = preg_replace('#<a [^>]*>#', '', $content);
$content = preg_replace('#</[^>]+>#', '', $content);
preg_match_all("#人氣：([0-9]*)\n([^\s]*)\n(.*)#m", ($content), $matches);

$latest_data = array();

$changed = false;
foreach ($matches[0] as $id => $data) {
    try {
        $board = strval($matches[2][$id]);
        $board = preg_replace('#\|(.*)$#', '', $board);
        $count = intval($matches[1][$id]);
        $name = iconv('UTF-8', 'UTF-8//IGNORE', strval($matches[3][$id]));

        if (RankData::search(array('board' => $board))->max('time')->count != $count) {
            $changed = true;
            RankData::insert(array(
                'time' => $time,
                'board' => $board,
                'count' => $count,
            ));
        }
    } catch (Pix_Table_DuplicateException $e) {
        RankData::search(array('time' => $time, 'board' => $board))->update(array('count' => $count));
    }

    $latest_data[] = array($board, $count, $name);
    TitleHistory::updateTitle($board, $time, $name);
}
if ($changed) {
    KeyValue::set('latest_hot', json_encode(array('time' => $time, 'boards' => $latest_data)));
}

echo '完成: ' . date('c', $time) . "\n";
