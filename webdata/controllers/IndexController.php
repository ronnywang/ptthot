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
	    throw new Exception('404');
	}
	$this->view->board = $board;
    }
}
