<?php

namespace EquivRoute;

class Dispatcher
{
    public $num = 0;

    public function __construct()
    {

    }

    public function dispatch($httpMethod, $uri, $status = -1)
    {
        global $_ROUTE;
        $uri = $this->uriEncode($uri);
        $uri = $this->virtualPath($uri);
        if (!is_numeric($uri)) {
            $uri = $this->virtualHost($uri);
        }

        $vars = array();
        foreach ($_ROUTE as $row) {
            $row[0] = is_array($row[0]) ? $row[0] : array($row[0]);
            $rule = preg_replace('/\//', '\/', $row[1]);
            $rule = $this->regExp($rule);
            if (in_array($httpMethod, $row[0])) {
                if (!is_string($uri)) {
                    print_r($uri);exit;
                }
                if (preg_match('/' . $rule . '/i', $uri, $matches)) {
                    $status = 1;
                    $uri = $row[2];
                    foreach ($matches as $key => $value) {
                        if (!is_numeric($key)) {
                            $vars[$key] = $value;
                        }
                    }
                }
            }
        }

        # $status = 0;
        $result = array($status);
        if (1 === $status) {
            $result[1] = $uri;
            $result[2] = $vars;

        } elseif (2 === $status) {
            $result[1] = array();
        }
        return $result;
    }

    public function regExp($rule)
    {
        $this->num++;
        $num = $this->num;
        $rl = '';
        if (preg_match('/\[.*\]/', $rule, $matches)) {
            $rule = preg_replace('/\[.*\]/', '__RULE'. $num .'__', $rule, 1);
            $vl = preg_replace('/\[/', '', $matches[0], 1);
            $vl = preg_replace('/\]/', '', $vl, 1);
            $rl = $this->regExp($vl);
        }

        if (preg_match_all('/\{[^\{]+\}/i', $rule, $matches)) {
            foreach ($matches[0] as $key => $value) {
                $val = preg_replace('/{|}/', '', $value);
                $vs = preg_replace('/\\\/', '\\\\\\\\', $val);
                $vs = preg_replace('/\+/', '\\\\+', $vs);
                $exp = explode(':', $vs);
                $ex = explode(':', $val);
                $count = count($exp);
                $name = array_shift($exp);
                $p = "($name)";
                $r = $ru = '[^\/]+';
                if (1 < $count) {
                    $ru = array_shift($exp);
                    $r = array_pop($ex);
                    $p .= ':' . $ru;
                }
                $replace = '(?P<\1>' . $r . ')';
                $rule = preg_replace('/{'. $p .'}/i', $replace, $rule);
            }
        }

        if ($rl) {
            $rule = preg_replace('/__RULE'. $num .'__/', '('. $rl .'|.*)', $rule);

        }
        return $rule;
    }

    public function virtualPath($uri)
    {
        global $_CONFIG;
        $PATH = $_CONFIG['virtual_paths'];
        $str = ltrim($uri, '/');
        $len = mb_strlen($str);

        $result = $uri;
        $index = null;
        // 忽略域名
        $ignore = isset($PATH['^']) ? $PATH['^'] : null;
        if ($ignore) {
            $ignore = is_array($ignore) ? $ignore : preg_split('/[\s,]+/', $ignore);
            if (in_array($_SERVER['HTTP_HOST'], $ignore)) {
                return $result;
            }
        }

        // URI 长度
        $paths = isset($PATH[$len]) ? $PATH[$len] : null;
        if ($paths) {
            foreach ($paths as $key => $value) {
                if (preg_match($key, $str)) {
                    $index = $value;
                    break;
                }
            }
        }

        // 其他规则
        $paths = isset($PATH['']) ? $PATH[''] : null;
        if ($paths) {
            foreach ($paths as $key => $value) {
                if (preg_match($key, $str)) {
                    $index = isset($PATH[$value]) ? $PATH[$value] : null;
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

    public function virtualHost($uri)
    {
        global $_CONFIG;
        $HOST = $_CONFIG['virtual_hosts'];
        $domain = $_CONFIG['host_domain'];

        // 自定义主机名
        $http_host = parse_url('//' . $_SERVER['HTTP_HOST'], PHP_URL_HOST);
        if (preg_match('/^\/php(|\/|\/.*)/i', $_SERVER['REQUEST_URI'])) {
            $http_host = 'ariesphp.loc.urlnk.com';
        }
        if (isset($_GET['http_host'])) {
            $http_host = $_GET['http_host'];
            // 不记住，临时预览
            if (!isset($_GET['setcookie'])) {
                setcookie('http_host', $http_host, time()+60*60*24*30, '/');
            }
        } elseif (isset($_COOKIE['http_host'])) {
            $http_host = $_COOKIE['http_host'];
        }

        // 虚拟主机配置
        defined('URL_HOST') ? : define('URL_HOST', $http_host);

        $result = $uri;
        $arr = array();
        $host_item = array_shift($HOST);
        foreach ($HOST as $key => $value) {
            if (is_numeric($key)) { // 前缀后缀分组
                foreach ($value as $prefix => $val) {
                    foreach ($val as $suffix => $v) {
                        foreach ($v as $k => $project) {
                            if (is_numeric($k)) {
                                $k = $project . $domain;
                            }
                            $file = $prefix . $project . $suffix;
                            # echo $file . PHP_EOL;
                            $arr[$k] = $file;
                        }
                    }
                }
                $key = null;
                # print_r($value);exit;

            } elseif (preg_match('/\//', $key)) { // 正则
                if (preg_match($key, URL_HOST, $matches)) {
                    $key = URL_HOST;
                    # print_r($matches);exit;
                }
                # $key = preg_replace('/\/+/', '', $key);

            } elseif (preg_match('/\s+/', $key)) { // 空格分隔多个
                $keys = preg_split('/\s+/', $key);
                $key = array_shift($keys);
                foreach ($keys as $k => $v) {
                    $arr[$v] = is_array($value) ? $host_item[$value[0]] : $value;
                }
                # print_r($keys);exit;
            }

            if (null !== $key) {
                $arr[$key] = is_array($value) ? $host_item[$value[0]] : $value;
            }
        }
        # print_r($arr);exit;

        // 包含入口文件
        if (array_key_exists(URL_HOST, $arr) && $result = include $arr[URL_HOST]) {
            # exit;

        } else {
            # echo __FILE__, PHP_EOL;exit;
        }
        return $result;
    }

    public function uriEncode($uri)
    {
        $len = mb_strlen($uri);
        if (false !== $pos = strpos($uri, '$')) {
            $start = mb_substr($uri, 0, $pos);
            $end = mb_substr($uri, $pos + 1, $len);
            $uri = "$start\$" . rawurlencode($end);
        }
        return $uri;
    }
}
