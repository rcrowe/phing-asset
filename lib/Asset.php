<?php

class Asset extends DataType
{
    private $assetsDir;
    private $paths;
    
    public $type;
    public $file;
    public $returnProperty;
    
    //Constants
    const ASSET_TYPE_CSS   = 0;
    const ASSET_TYPE_JS    = 1;
    const ASSET_TYPE_IMAGE = 2;
    const ASSET_TYPE_OTHER = 3;
    
    public function __construct()
    {
        $this->assetsDir = $assetsDir;
        $this->paths     = $paths;
    }
    
    public function setJs($file)
    {
        $this->type = self::ASSET_TYPE_JS;
        $this->file = $file;
    }
    
    public function setCss($file)
    {
        $this->type = self::ASSET_TYPE_CSS;
        $this->file = $file;
    }
    
    public function setImage($file)
    {
        $this->type = self::ASSET_TYPE_IMAGE;
        $this->file = $file;
    }
    
    public function setOther($file)
    {
        $this->type = self::ASSET_TYPE_OTHER;
        $this->file = $file;
    }
    
    public function setReturnProperty($prop)
    {
        $this->returnProperty = $prop;
    }
}

?>