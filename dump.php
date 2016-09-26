<?php

include(__DIR__ . '/init.php');
Pix_Table::$_save_memory = true;
$last_time = intval(KeyValue::get('snapshot_at'));
putenv('HTTPS_PROXY=');

// dump count
while (true) {
    $min_time = RankData::search("`time` > {$last_time}")->min('time')->time;
    if (!$min_time) {
        break;
    }
    $month_start = mktime(0, 0, 0, date('m', $min_time), 1, date('Y', $min_time));
    $month_end = strtotime('+1 month', $month_start);

    $filename = 'ptthot-' . date('Ym', $month_start) . '.csv.gz';
    $temp = tmpfile();
    $meta_data = stream_get_meta_data($temp);
    $tmp_filename = $meta_data['uri'];
    $stream = gzopen($tmp_filename, 'w');
    fwrite($stream, "#board,timestamp,count\n");
    $prev_count = null;
    foreach (RankData::search("`time` >= $month_start AND `time` < $month_end")->order(array('board', 'time'))->volumemode(10000) as $rankdata) {
        $last_time = max($last_time, $rankdata->time);
        if (!is_null($prev_count) and $rankdata->count == $prev_count) {
            continue;
        }
        $prev_count = $rankdata->count;
        fwrite($stream, "{$rankdata->board},{$rankdata->time},{$rankdata->count}\n");
    }
    fclose($stream);
    S3Lib::putFile($tmp_filename, 's3://ronnywang-ptthot/' . $filename);
    fclose($temp);

    KeyValue::set('snapshot_at', $last_time);
}

//dump titlehistory
$filename = 'ptthot-title.csv.gz';
$temp = tmpfile();
$meta_data = stream_get_meta_data($temp);
$tmp_filename = $meta_data['uri'];
$stream = gzopen($tmp_filename, 'w');
fwrite($stream, "#board,timestamp,title\n");
foreach (TitleHistory::search(1)->order(array("board", "title"))->volumemode(10000) as $titlehistory) {
    fputcsv($stream, array($titlehistory->board, $titlehistory->time, $titlehistory->title));
}
fclose($stream);
S3Lib::putFile($tmp_filename, 's3://ronnywang-ptthot/' . $filename);
S3Lib::buildIndex('s3://ronnywang-ptthot/');
fclose($temp);
