<?php

class IndexController extends Pix_Controller
{
    public function indexAction()
    {
    }

    public function boardAction()
    {
        list(, /*board*/, $board) = explode('/', $this->getURI());

        if (!RankData::search(array('board' => $board))->first()) {
            return $this->redirect('/');
        }
        $this->view->board = $board;
        $this->view->from = $_GET['from'] ? strtotime($_GET['from']) : 0;
        $this->view->to = $_GET['to'] ? strtotime($_GET['to']) : 0;
    }

    public function searchAction()
    {
        return $this->redirect('/board/' . urlencode($_GET['q']));
    }

    public function healthAction()
    {
        $latest_hots = json_decode(KeyValue::get('latest_hot'));
        $latesttime = $latest_hots->time;

        if (time() - $latesttime > 3600) {
            echo 'error from ' . date('c', $latesttime);
        } else {
            echo 'ok';
        }
    }
}
