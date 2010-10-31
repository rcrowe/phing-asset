<?php

class File extends DataType
{
    public $file;
    
    public function __construct()
    {
        
    }
    
    public function setName($file)
    {
        $this->file = $file;
    }
}

?>