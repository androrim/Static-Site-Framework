<?php

namespace StaticSiiteFramework;

use StaticSiiteFramework\Request;

class StaticSiiteFramework
{

    /**
     *
     * @var mixed
     */
    public static $config;

    /**
     *
     * @var SiteStaticFramework\Request
     */
    public static $request;
    public static $pageRequested;

    public static function init()
    {
        $config = (object) require APP_ROOT_BASE_DIR . '/config/app.php';
        
        self::$config = self::parseConfig($config);
        self::$request = new Request(self::$config);
        self::$pageRequested = self::$request->getPageRequest();
        

        self::loadRequestedPage();
    }

    private static function parseConfig()
    {

        $config->App['pages_dir'] = APP_ROOT_BASE_DIR . "/{$config->App['dir']}"
                . "/{$config->App['site']['dir']}"
                . "/{$config->App['site']['theme']}"
                . "/{$config->App['site']['pages']['dir']}";

        $config->App['home_dir'] = APP_ROOT_BASE_DIR . "/{$config->App['dir']}"
                . "/{$config->App['site']['dir']}"
                . "/{$config->App['site']['theme']}"
                . "/{$config->App['site']['pages']['dir']}"
                . "/{$config->App['site']['pages']['home']}";

        $config->App['error_dir'] = APP_ROOT_BASE_DIR . "/{$config->App['dir']}"
                . "/{$config->App['site']['dir']}"
                . "/{$config->App['site']['theme']}"
                . "/{$config->App['site']['pages']['dir']}"
                . "/{$config->App['site']['pages']['error']}";

        $config->App['webroot_dir'] = APP_ROOT_BASE_DIR . "/{$config->App['webroot']['dir']}";

        $config->App['assets']['images']['dir'] = APP_ROOT_BASE_DIR . "/{$config->App['webroot']['dir']}"
                . "/{$config->App['webroot']['assets']['images']}";

        $config->App['assets']['styles']['dir'] = APP_ROOT_BASE_DIR . "/{$config->App['webroot']['dir']}"
                . "/{$config->App['webroot']['assets']['styles']}";

        $config->App['assets']['javascript']['dir'] = APP_ROOT_BASE_DIR . "/{$config->App['webroot']['dir']}"
                . "/{$config->App['webroot']['assets']['javascript']}";

        $config->App['assets']['images']['url'] = self::baseUrl() . "/{$config->App['webroot']['dir']}"
                . "/{$config->App['webroot']['assets']['images']}";

        $config->App['assets']['styles']['url'] = self::baseUrl() . "/{$config->App['webroot']['dir']}"
                . "/{$config->App['webroot']['assets']['styles']}";

        $config->App['assets']['javascript']['url'] = self::baseUrl() . "/{$config->App['webroot']['dir']}"
                . "/{$config->App['webroot']['assets']['javascript']}";

        return $config;
    }
    
    public static function baseUrl()
    {
        return self::$request->protocol('//') . $_SERVER['SERVER_NAME'] . self::$config->App['base'];
    }

    public static function isHome()
    {
        return self::$pageRequested['page'] == self::$config->App['site']['pages']['home'];
    }

    private static function loadRequestedPage()
    {
        $homeIndex = self::$config->App['home_dir'] . '/index.php';

        if (self::isHome() && !file_exists($homeIndex)) {
            die('You should create file ' . $homeIndex);
        }

        $page = self::$pageRequested['page'];
        $params = (array) self::$pageRequested['params'];

        if (!empty($params)) {
            $values = array_values($params);
            $firsParam = array_shift($values);

            self::loadPage("{$page}/{$firsParam}", true);
            exit();
        }

        self::loadPage($page);
        exit();
    }

    public static function loadPage($page, $tryRequested = false)
    {
        $pagesDir = self::$config->App['pages_dir'];
        $errorDir = self::$config->App['error_dir'];
        $homeIndex = self::$config->App['home_dir'] . '/index.php';

        if (!$page) {
            if (file_exists("{$errorDir}/404.php")) {
                require "{$errorDir}/404.php";
                exit();
            }

            if (file_exists("{$errorDir}/index.php")) {
                require "{$errorDir}/index.php";
                exit();
            }

            if (file_exists($homeIndex)) {
                require $homeIndex;
                exit();
            }
        }

        if (file_exists("{$pagesDir}/{$page}/index.php")) {
            require "{$pagesDir}/{$page}/index.php";
            exit();
        }

        if (file_exists("{$pagesDir}/{$page}.php")) {
            require "{$pagesDir}/{$page}.php";
            exit();
        }

        $pageRequested = self::$pageRequested;

        if ($tryRequested = file_exists("{$pagesDir}/{$pageRequested['page']}/index.php")) {
            require "{$pagesDir}/{$pageRequested['page']}/index.php";
            exit();
        }

        if (file_exists($homeIndex)) {
            require $homeIndex;
            exit();
        }
    }

    public static function loadStyle($name)
    {
        $url = self::$config->App['assets']['steyles']['url'];
        $dir = self::$config->App['assets']['steyles']['dir'];
        $style = '';
        
        if (file_exists("{$dir}/{$name}")) {
            $style = "<link rel=\"stylesheet\" href=\"{$url}{$name}\"/>";
        }
        else if (file_exists("{$dir}/{$name}.css")) {
            $style = "<link rel=\"stylesheet\" href=\"{$url}{$name}.css\"/>";
        }
        
        echo $style;
        
    }

}
