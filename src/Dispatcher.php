<?php

namespace EquivRoute;

class Dispatcher
{
    public function __construct()
    {

    }

    public function dispatch($httpMethod, $uri, $status = 1)
    {
        # print_r(get_defined_vars());

        $uri = $this->virtualPath($uri);
        # $status = 0;
        $result = array($status);
        if (1 === $status) {
            $result[1] = $uri;
            $result[2] = array();

        } elseif (2 === $status) {
            $result[1] = array();
        }
        return $result;
    }

    public function virtualPath($uri)
    {
        global $_CONFIG;
        $PATH = $_CONFIG['virtual_paths'];
        $str = ltrim($uri, '/');
        $len = mb_strlen($str);

        $result = $uri;
        $index = null;
        $paths = isset($PATH[$len]) ? $PATH[$len] : null;
        if ($paths) {
            foreach ($paths as $key => $value) {
                if (preg_match($key, $str)) {
                    $index = $value;
                    break;
                }
            }
        }

        if ($index) {
            $result = include $index;
        }
        # print_r(get_defined_vars());
        return $result;
    }
}
