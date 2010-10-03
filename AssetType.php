<?php

class AssetType extends DataType
{
    //Constants for the type of asset
    const ASSET_TYPE_CSS   = 0;
    const ASSET_TYPE_JS    = 1;
    const ASSET_TYPE_IMAGE = 2;
    const ASSET_TYPE_OTHER = 3;

    public $assetType;      //Type of asset, either css,image,js,other
    public $filePath;       //Path to file
    public $returnProperty; //Return property to set URL

    //Set the CSS file to generate URL for
    public function setCss($file)
    {
        $this->assetType = self::ASSET_TYPE_CSS;
        $this->filePath  = $file;
    }
    
    public function setJs($file)
    {
        $this->assetType = self::ASSET_TYPE_JS;
        $this->filePath  = $file;
    }
    
    public function setImage($file)
    {
        $this->assetType = self::ASSET_TYPE_IMAGE;
        $this->filePath  = $file;
    }
    
    public function setOther($file)
    {
        $this->assetType = self::ASSET_TYPE_OTHER;
        $this->filePath  = $file;
    }
    
    public function setReturnProperty($property)
    {
        $this->returnProperty = $property;
    }
}

?>