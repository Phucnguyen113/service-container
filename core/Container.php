<?php
namespace Phucrr\Php;

use Phucrr\Php\ContextualBindingBuilder;

class Container {
    
    public $bindings = [];
    protected $instances = [];
    protected static $instance;
    protected $buildStack = [];
    public $aliases = [];
    public $contextual = [];

    public function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;
        return $instance;
    }
    /**
     * Resolve the given type from the container
     *
     * @param string $abstract
     *
     * @return mixed
     */
    public function make($abstract)
    {

        $abstract = $this->getAlias($abstract);
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        $concrete = $this->getConcrete($abstract);
        if ($concrete instanceof \Closure || $concrete === $abstract) {
            $instance = $this->build($concrete);
        } else {
            $instance = $this->make($concrete);
        }

        if (isset($this->bindings[$abstract]) && $this->bindings[$abstract]['share']) {
            
            $this->instances[$abstract] = $instance;
        }
        return $instance;
    }

    /**
     * Get concrete by abstract
     *
     * @param string $abstract
     */
    private function getConcrete($abstract)
    {
        if (! is_null($context = $this->getContextualBinding($abstract))) {
            return $context;
        }

        if (isset($this->bindings[$abstract])){
            return $this->bindings[$abstract]['concrete'];
        }
        return $abstract;
    }

    /**
     * Binding concrete with abstract
     *
     * @param string $abstract
     * @param string $concrete
     */
    public function bind($abstract, $concrete = null, $share = false)
    {
        if (is_null($concrete)) {
            $concrete = $abstract;
        }

        if (!$concrete instanceof \Closure) {
            $concrete = $this->getClosure($abstract, $concrete);
        }

        $this->bindings[$abstract] = compact('concrete', 'share');
    }

    /**
     * Get closure binding
     *
     * @param string $abstract
     * @param string $concrete
     */
    private function getClosure($abstract, $concrete)
    {
        return function ($container) use ($abstract, $concrete) {
            if ($abstract === $concrete) {
                return $container->build($concrete);
            }
            return $container->make($concrete);
        };
    }

    /**
     * Build concrete
     *
     * @param string|Closure $concrete
     * 
     * @return object
     */
    private function build($concrete)
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this);
        }

        $instance = new \ReflectionClass($concrete);
        if (!$instance->isInstantiable()) {
            throw new \Exception('instance not instantiable');
        }

        $this->buildStack[] = $concrete;

        $constructor = $instance->getConstructor();
        if ( $constructor === null) {
            return $instance->newInstance();
        }
       
        $dependencies = [];
        foreach ($constructor->getParameters() as $key => $param) {
            if ($class = $param->getClass()) {
                $dependencies[]= $this->make($class->name);
            } elseif ($param->isDefaultValueAvailable()) {
                $dependencies[] = $param->getDefaultValue();
            }
        }

        array_pop($this->buildStack);

        return $instance->newInstanceArgs($dependencies);
    }

    /**
     * Get the alias of abstract
     * 
     * @param string $alias
     * 
     * @return string
     */
    private function getAlias($alias) {
        if (!isset($this->aliases[$alias])) {
            return $alias;
        }
        return $this->getAlias($this->aliases[$alias]);
    }

    /**
     * Register a shared binding in the container.
     * 
     * @param  string  $abstract
     * @param  \Closure|string|null  $concrete
     * 
     * @return void
     */
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    public function when($concrete)
    {
        $aliases = [];

        $concrete = is_array($concrete) ? $concrete : [$concrete];
   
        foreach ($concrete as $value) {
            $aliases[] = $value;
        }
        return (new ContextualBindingBuilder($this, $aliases));
    }

    public function addContextualBinding($concrete, $abstract, $implementation)
    {
        $this->contextual[$concrete][$this->getAlias($abstract)] = $implementation;
    }

    public function getContextualBinding($abstract)
    {   
        $context = end($this->buildStack);
        if (isset($this->contextual[$context][$abstract])) {
            return $this->contextual[$context][$abstract];
        }
        return null;
    }

}
