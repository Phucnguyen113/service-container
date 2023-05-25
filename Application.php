<?php
require_once 'Container.php';
require_once 'ContextualBindingBuilder.php';
class Application extends Container{
    public function __construct()
    {
        static::$instance = $this;
        // $this->bindingAliases();
    }

    private function bindingAliases()
    {
        $aliases = ['a' => [AChild::class]];
        foreach ($aliases as $key => $alias) {
            foreach ($alias as $concrete) {
               $this->aliases[$concrete] = $key;
            }
        }
    }

    public function getInstance()
    {
        return static::$instance;
    }
}
interface a {

}
class child1 implements a {

}

class child2 implements a {

}
echo '<pre>';
$app = new Application();
$app->when('AController')->needs(a::class)->give(child1::class);
$app->when('BController')->needs(a::class)->give(child2::class);

class AController {
    public $a;
    public function __construct(a $a)
    {
        $this->a = $a;
    }
}

class BController {
    public $a;
    public function __construct(a $a)
    {
        $this->a = $a;
    }
}
print_r($app->make(BController::class));