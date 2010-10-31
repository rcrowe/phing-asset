<?php

class Asset extends DataType
{
    private $assetsDir;
    private $assetFolder;
    private $url;
    private $paths;
    private $type;
    private $file;
    private $returnProperty;
    
    //Constants
    const ASSET_TYPE_CSS   = 0;
    const ASSET_TYPE_JS    = 1;
    const ASSET_TYPE_IMAGE = 2;
    const ASSET_TYPE_OTHER = 3;
    
    public function __construct(){}
    
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
    
    public function main($assetDir, $url, $paths)
    {
        $this->assetsDir = $assetDir;
        $this->url       = $url;
        $this->paths     = $paths;
        
        if(empty($this->assetsDir))
        {
            throw new BuildException("Make sure you set the path to your assets", $this->location);
        }
        
        if(empty($this->url))
        {
            throw new BuildException("Make sure you set the URL to your assets", $this->location);
        }
        
        
        $asset_file = $this->checkFileExists();
        
        $generated_url  = $this->generateURL($asset_file);
        
        //Set the generated URL to use in the buildfile
        $this->project->setProperty($this->returnProperty, $generated_url);
    }
    
    public function checkFileExists()
    {
        //Get the correct path to asset
        switch($this->type)
        {
            case Asset::ASSET_TYPE_CSS:         $this->assetFolder = $this->paths['css'];
                                                break;
                                            
            case Asset::ASSET_TYPE_JS:          $this->assetFolder = $this->paths['js'];
                                                break;
                                            
            case Asset::ASSET_TYPE_IMAGE:       $this->assetFolder = $this->paths['images'];
                                                break;

            default:                            $folder = '';
        }
        
        //Path to file
        $file = new PhingFile($this->assetsDir.'/'.$this->assetFolder.$this->file);
        
        //Check file exists
        if(!$file->exists())
        {
            throw new BuildException("Unable to find asset file: ".$file->getAbsolutePath());
        }
        
        //Check we can read it
        if(!$file->canRead())
        {
            throw IOException("Unable to read asset file: ".$file->getPath());
        }
        
        return $file;
    }
    
    public function generateURL(PhingFile $file)
    {
        //Build modified param to help with caching
        $modtime   = $file->lastModified();
        $mod_param = ($modtime !== 0) ? "?$modtime" : '';
        
        //Build path to actual file
        $folder = (!empty($this->assetFolder)) ? $this->assetFolder : '';
        $url_path = $folder.$this->file;
        
        return sprintf("http://%s%s%s", $this->url, $url_path, $mod_param);
    }
}

?>