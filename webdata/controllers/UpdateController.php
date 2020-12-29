<?php

class UpdateController extends Pix_Controller
{
    public function remoteAction()
    {
        include(__DIR__ . '/Big52003.php');
        if (!getenv('REMOTE_KEY') or $_POST['REMOTE_KEY'] != getenv('REMOTE_KEY')) {
            return $this->notfound();
        }
        $content = $_POST['content'];
        $doc = new DOMDocument;
        @$doc->loadHTML($content);

        $time = time(); // 現在人氣是即時的

        $latest_data = array();
        $changed = false;
        foreach ($doc->getElementsByTagName('a') as $a_dom) {
            if ($a_dom->getAttribute('class') != 'board') {
                continue;
            }
            $div_doms = $a_dom->getElementsByTagName('div');
            if ($div_doms->item(0)->getAttribute('class') != 'board-name') {
                throw new Exception("1st is not board-name");
            }
            $board = trim($div_doms->item(0)->nodeValue);
            if ($div_doms->item(1)->getAttribute('class') != 'board-nuser') {
                throw new Exception("2nd is not board-nuser");
            }
            $count = intval($div_doms->item(1)->nodeValue);
            if ($div_doms->item(3)->getAttribute('class') != 'board-title') {
                throw new Exception("4th is not board-title");
            }
            $name = trim($div_doms->item(3)->nodeValue);
            $name = preg_replace('#^◎#', '', $name);

            if (RankData::search(array('board' => $board))->max('time')->count != $count) {
                $changed = true;
                try {
                    RankData::insert(array(
                        'time' => $time,
                        'board' => $board,
                        'count' => $count,
                    ));
                } catch (Pix_Table_DuplicateException $e) {
                    RankData::search(array('time' => $time, 'board' => $board))->update(array('count' => $count));
                }
            }

            $latest_data[] = array($board, $count, $name);
            TitleHistory::updateTitle($board, $time, $name);
        }
        if ($changed) {
            KeyValue::set('latest_hot', json_encode(array('time' => $time, 'boards' => $latest_data)));
            echo '完成: ' . date('c', $time) . "\n";
        } else {
            echo '未更新資料';
        }

    }
}
