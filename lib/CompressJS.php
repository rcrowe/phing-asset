<?php

require 'File.php';

class CompressJS
{
    public $file;
    public $files = array();
    
    public function __construct()
    {
        
    }
    
    public function setFile($file)
    {
        $this->file = $file;
    }
    
    public function createFile()
    {
        $num = array_push($this->files, new File());
        return $this->files[$num-1];
    }
}

?>