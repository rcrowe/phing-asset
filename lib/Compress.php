<?php

require 'CompressJS.php';
require 'CompressCSS.php';

class Compress extends DataType
{
    private $compress_js  = array();
    private $compress_css = array();
    
    public function __construct()
    {
        
    }
    
    public function createJS()
    {
        $num = array_push($this->compress_js, new CompressJS());
        return $this->compress_js[$num-1];
    }
    
    public function createCSS()
    {
        $num = array_push($this->compress_css, new CompressCSS());
        return $this->compress_css[$num-1];
    }
}

?>