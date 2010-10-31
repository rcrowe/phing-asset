<?php

require_once 'Asset.php';
require_once 'Compress.php';

class Assets extends Task
{
    //Paths under the asset folder
    private $paths = array(
        'js'     => 'js/',
        'css'    => 'css/',
        'images' => 'images/'
    );
    
    //Info <assets> tag
    private $assetDir; //Holds PhingFile instance of asset directory
    private $url;      //URL to assets
    
    //Details for <compress>
    private $compress = array();
    
    //Details for <asset>
    private $assets = array();
    
    //Need this to satisfy Phing
    public function init()
    {}
    
    //Set the directory to your assets
    public function setDir(PhingFile $dir)
    {
        if(!$dir->exists())
        {
            throw new BuildException("Can not find asset directory: ".$dir->getAbsolutePath(), $this->location);
        }
        
        $this->assetDir = $dir->getAbsolutePath();
    }
    
    //Set the URL to generate assets URLS with
    public function setUrl($url)
    {
        $url = str_replace("\\", "/", $url);
        $this->url = (substr($url, -1) != '/') ? $url.'/' : $url;
    }
    
    //Handle <asset> tag
    public function createAsset()
    {
        $num = array_push($this->assets, new Asset($this->assetDir, $this->url, $this->paths));
        return $this->assets[$num-1];
    }
    
    //Handle <compress> tag
    public function createCompress()
    {
        $num = array_push($this->compress, new Compress());
        return $this->compress[$num-1];
    }
    
    public function main()
    {
        //Handle any compression
        if(!empty($this->compress))
        {
            foreach($this->compress as $compress)
            {
                $compress->main($this->assetDir, $this->paths);
            }
        }
        
        //Handle building URLs to assets
        if(!empty($this->assets))
        {
            foreach($this->assets as $asset)
            {
                $asset->main($this->assetDir, $this->url, $this->paths);
            }
        }
    }
}

?>