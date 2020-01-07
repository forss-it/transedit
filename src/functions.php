<?php

if (! function_exists('transEdit')) {
    /**
     * Get an instance of TransEdit.
     * @return \Dialect\TransEdit\TransEdit
     */
    function transEdit($key = null, $val = null, $locale = 'current')
    {
        $app = app('transedit');
       
        if ($key !== null && ! $val) {
            return $app->locale($locale)->key($key);
        } elseif ($key !== null && $val) {
            return $app->locale($locale)->key($key, $val);
        }

        return $app;
    }
}
