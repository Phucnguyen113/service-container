<?php
namespace Phucrr\Php;

trait Path {
    public function path()
    {
        return $this->basePath;
    }

    public function configPath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'config'.($path ? '/'.trim($path, '\/') : '');
    }

    public function publicPath()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'public';
    }

    public function resourcePath($path = '')
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'resources'.($path ? '/'.trim($path, '\/') : '');
    }
}