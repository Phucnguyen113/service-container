<?php

use Phucrr\Php\Application;
use Phucrr\Php\Support\View;

    if (!function_exists('app')) {
        /**
         * Return the application instance or binding instance
         * 
         * @param string $abstract
         * 
         * @return mixed
         */
        function app($abstract = null)
        {
            if (!$abstract) {
                return Application::getInstance();
            }
            return Application::getInstance()->make($abstract);
        }
    }

    if (!function_exists('view')) {
        /**
         * Return the specific view by name
         * 
         * @param string $view the name of the view
         * @param array $data the data to binding to view
         * 
         * @return View
         */
        function view($view, $data = [])
        {
            return (new View(app('path.resource'), $view, $data));
        }
    }

?>