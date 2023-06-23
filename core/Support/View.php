<?php
namespace Phucrr\Php\Support;

class View {

    /**
     * The path to the view file
     * @var string
     */
    public $path;

    /**
     * The name of the view
     * @var string
     */
    public $view;

    /**
     * The data binding in the view
     * @var array
     */
    public $data;

    /**
     * @param string $path
     * @param string $view
     * @param array $data
     * 
     * @return null
     */
    public function __construct(string $path, string $view, array $data = [])
    {
        $this->path = $path;
        $this->view = $view;
        $this->data = $data;
    }

    /**
     * Render the contents html in views
     * 
     * @return string
     */
    public function render()
    {
        ob_start();
        include '../resources/views/test.php';
        $result = ob_get_clean();

        $this->prepareSecureVariable($result);
        $this->prepareUnsecureVariable($result);
      
        $this->preparePhpBlock($result);
        
        return $result;
    }

    /**
     * Print the specific variable, make sure htmlspecialchars have been handled
     * 
     * @param string &$result
     * 
     * @return null
     */
    private function prepareSecureVariable(string &$result)
    {
        extract($this->data);
        preg_match_all('/\{\{(\$(\w+)+)\}\}/', $result, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $key => $match) {
                $result = str_replace($matches[0][$key], eval("return htmlspecialchars($match, ENT_QUOTES, 'UTF-8');"), $result);       
            }
        }
    }

    /**
     * Print the specific variable not security about XSS
     * 
     * @param string &$result
     * 
     * @return null
     */
    private function prepareUnsecureVariable(string &$result)
    {
        extract($this->data);
        preg_match_all('/\{\!\!(\$(\w+)+)\!\!\}/', $result, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $key => $match) {
                $result = str_replace($matches[0][$key], eval("return $match;"), $result);       
            }
        }
    }

    /**
     * Convert @php in template
     * @param string &$result
     * 
     * @return null
     */
    private function preparePhpBlock(string &$result)
    {
        extract($this->data);
        preg_match_all('/(?<!@)@php(.*?)@endphp/s', $result, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $key => $match) {
                ob_start();
                eval("?><?php $match?>");
                $replacement = ob_get_clean();
                $result = str_replace($matches[0][$key], $replacement, $result);       
            }
        }
    }

    public function __toString()
    {
        return $this->render();
    }
}