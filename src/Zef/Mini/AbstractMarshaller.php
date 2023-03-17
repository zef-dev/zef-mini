<?php

namespace Zef\Mini;


abstract class AbstractMarshaller implements IMarshaller 
{
    
    public function marshalAll( $data) {
        return array_map( function ( $row) {
            return $this->marshal( $row);
        }, $data);
    }
    
    // UTIL
    public function __toString()
    {
        return get_class( $this).'[]';
    }
}