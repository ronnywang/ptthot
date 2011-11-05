<?php 

/**
 * Pix_AlertException 
 *
 * 會在網頁中顯示警告視窗的 Excption ，使用方法為 throw new Pix_AlertException('錯誤訊息', '跳完訊息後所跳去的網頁');
 * 當 $url 指定為 Pix_AlertException::ALERT_EXIT 的話，表示警告視窗跳完就關掉網頁
 * 
 * @uses Exception
 * @version $id$
 * @copyright 2003-2009 PIXNET
 * @author Shang-Rung Wang <srwang@pixnet.tw> 
 */
class Pix_AlertException extends Exception
{
    protected $_url;
    const ALERT_EXIT = -1;

    public function __construct($message, $url = null)
    {
	parent::__construct($message);
	$this->_url = $url;
    }

    public function getURL()
    {
	return $this->_url;
    }
}


