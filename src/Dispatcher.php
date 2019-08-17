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
}
