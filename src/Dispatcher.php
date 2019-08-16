<?php

namespace EquivRoute;

class Dispatcher
{
    public function __construct()
    {

    }

    public function dispatch($httpMethod, $uri)
    {
        # print_r(get_defined_vars());
        $result = array(0);
        $result[0] = 1;
        $result[1] = $uri;
        $result[2] = array();
        return $result;
    }
}
