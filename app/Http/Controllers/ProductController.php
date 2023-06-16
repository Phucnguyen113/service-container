<?php
namespace App\Http\Controllers;

use Phucrr\Php\Contracts\RequestContract;

class ProductController {
    public function index($id, RequestContract $request, $e = 'eee', $w ='w')
    {
        echo '<pre>';
        echo 'product controller ibndex';
        print_r($request->uri());
        die();
    }
}