<?php

class HttpRequestParser
{
    public static function preparePostData( $arrayData )
    {
        $postdata = http_build_query(
            $arrayData
        );
        
        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );
        
        return stream_context_create($opts);
    }
}