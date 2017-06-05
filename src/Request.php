<?php

namespace SiteStaticFramework;

/**
 *
 * @author Leandro de Amorim <androrim@gmail.com>
 */
class Request
{

    public $protocol;
    private $server;
    private $config;
    private $routes;

    public function __construct($config)
    {
        $this->config = $config;
        $this->routes = json_decode(file_get_contents(APP_ROOT_BASE_DIR . '/config/routes.json'), true);

        $this->server = $_SERVER;
        $this->protocol = isset($this->server['HTTPS']) && $this->server['HTTPS'] != 'off' ? 'https' : 'http';
    }

    public function protocol($after = '')
    {
        return $this->protocol . $after;
    }

    public function baseUrl()
    {
        return self::$request->protocol('//') . $this->server['SERVER_NAME'] . $this->config->App['base'];
    }


    public function getPageRequest()
    {
        $pagesDir = APP_ROOT_BASE_DIR . "/{$this->config->App['dir']}"
                . "/{$this->config->App['site']['dir']}"
                . "/{$this->config->App['site']['theme']}"
                . "/{$this->config->App['site']['pages']['dir']}";

        $page = null;
        $params = array();
        $requested = null;

        $result = array(
            'page' => null,
            'params' => array()
        );

        $arrayRequest = $this->parseRequest();
        
        foreach ($arrayRequest as $i => $req) {
            
            if ($req == '') {
                $result['page'] = $this->config->App['site']['pages']['home'];
                break;
            }

            if ($requested) {
                $requested .= DIRECTORY_SEPARATOR . $req;
            }
            else {
                $requested = $req;
            }

            if (is_dir("{$pagesDir}/{$requested}")) {
                $result['page'] = $requested;
            }
            else {
                $route = "{$this->config->App['site']['pages']['dir']}/" . $result['page'];
                
                if (isset($this->routes[$route]) && isset($this->routes[$route][$i - 1])) {
                    $result['params'][$this->routes[$route][$i - 1]] = $req;
                }
            }
        }

        if ($result['page']) {
            return $result;
        }
    }

    private function parseRequest()
    {
        $request = str_replace("{$this->config->App['base']}/", '', $this->server['REQUEST_URI']);
        $arrayRequest = explode('/', $request);

        return $arrayRequest;
    }
}
