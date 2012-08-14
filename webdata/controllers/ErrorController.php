<?php

class ErrorController extends Pix_Controller
{
    public function errorAction()
    {
	throw $this->view->exception;
    }
}
