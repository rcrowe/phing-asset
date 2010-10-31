<?php

require_once 'File.php';

class CompressJS
{
    public $file;
    public $files = array();
    public $overwrite = false;
    
    public function __construct(){}
    
    public function setFile($file)
    {    
        $this->file = $file;
    }
    
    public function setOverwrite($over)
    {
        if(is_bool($over))
        {
            $this->overwrite = $over;
        }
    }
    
    public function createFile()
    {
        $num = array_push($this->files, new File());
        return $this->files[$num-1];
    }
}

?>