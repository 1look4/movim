<?php

namespace Movim;

use Movim\Widget\Wrapper;

class RPC
{
    protected static $funcalls;

    public static function call($funcname)
    {
        if (!is_array(self::$funcalls)) {
            self::$funcalls = [];
        }

        $args = func_get_args();
        array_shift($args);

        if (self::filter($funcname, $args)) {
            $funcall = [
                'func' => $funcname,
                'params' => $args,
            ];

            self::$funcalls[] = $funcall;
        }
    }

    /**
     * Check if the event is not already called
     */
    private static function filter($funcname, $args)
    {
        foreach (self::$funcalls as $f) {
            if (isset($f['func'])
                && isset($f['params'])
                && $f['func'] == $funcname
                && $f['params'] === $args
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sends outgoing requests.
     */
    public static function commit()
    {
        return self::$funcalls;
    }

    public static function clear()
    {
        self::$funcalls = [];
    }

    /**
     * Handles incoming requests.
     */
    public function handle_json($request)
    {
        // Loading the widget.
        if (isset($request->widget)) {
            $widget_name = (string)$request->widget;
        } else {
            return;
        }

        $result = [];

        // Preparing the parameters and calling the function.
        if (isset($request->params)) {
            $params = (array)$request->params;

            foreach ($params as $p) {
                if (is_object($p) && isset($p->container)) {
                    array_push($result, (array)$p->container);
                } else {
                    array_push($result, $p);
                }
            }
        }

        $widgets = new Wrapper;
        $widgets->runWidget($widget_name, (string)$request->func, $result);
    }
}

?>
