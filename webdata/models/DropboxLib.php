<?php

class DropboxLib
{
    public function putFile($file, $target)
    {
        $storage = new \Dropbox\OAuth\Storage\ArrayStorage();
        $config = new StdClass;
        $config->oauth_token = getenv('DROPBOX_ACCESS_KEY');
        $config->oauth_token_secret = getenv('DROPBOX_ACCESS_SECRET');
        $storage->set($config, 'access_token');
        $OAuth = new \Dropbox\OAuth\Consumer\Curl(getenv('DROPBOX_KEY'), getenv('DROPBOX_SECRET'), $storage, $callback);
        $dropbox = new \Dropbox\API($OAuth);
        $stream = fopen($file, 'r');
        $result = $dropbox->putStream($stream, $target);
    }
}
