<?php

namespace Phucrr\Php\Contracts;

interface RequestContract {
    public function all();

    public function get($key, $default);

    public function method();

    public function uri();
}