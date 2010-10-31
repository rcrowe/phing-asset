<?php

require 'Asset.php';
require 'Compress.php';

class Assets extends Task
{
    //Paths
    private $paths = array(
        'js'     => 'js/',
        'css'    => 'css/',
        'images' => 'images/'
    );
    
    private $phingDir;
    
    //Details for <compress>
    private $compress = array();
    
    //Details for <asset>
    private $assetsDir;
    private $url;
    private $assets = array();
    private $assetFolder;
    
    public function init()
    {}
    
    //Set the directory to your assets
    public function setDir(PhingFile $dir)
    {
        if(!$dir->exists())
        {
            throw new BuildException("Can not find asset directory: ".$dir->getAbsolutePath(), $this->location);
        }
        else
        {
            $this->phingDir = $dir;
            $this->assetsDir = $dir->getAbsolutePath().'/';
        }
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
        $num = array_push($this->assets, new Asset());
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
        if(empty($this->assetsDir))
        {
            throw new BuildException("Make sure you set the path to your assets", $this->location);
        }
        
        if(empty($this->url))
        {
            throw new BuildException("Make sure you set the URL to your assets", $this->location);
        }
        
        //Do we want to compress any files
        if(!empty($this->compress))
        {
            foreach($this->compress as $compress)
            {
                //Handle JS compression
                foreach($compress->compress_js as $js)
                {
                    
                }
            }
        }
        
        //Generate URL for each file
        foreach($this->assets as $asset)
        {
            $file = $this->checkFileExists($asset);
            
            $url  = $this->generateURL($asset, $file);
            
            //Set the generated URL to use in the buildfile
            $this->project->setProperty($asset->returnProperty, $url);
        }
    }
    
    public function checkFileExists(Asset $asset)
    {
        //Get the correct path to asset
        switch($asset->type)
        {
            case Asset::ASSET_TYPE_CSS:         $this->assetFolder = $this->paths['css'];
                                                break;
                                            
            case Asset::ASSET_TYPE_JS:          $this->assetFolder = $this->paths['js'];
                                                break;
                                            
            case Asset::ASSET_TYPE_IMAGE:       $this->assetFolder = $this->paths['images'];
                                                break;

            default:                            $this->assetFolder = '';
        }
        
        //Path to file
        $file = new PhingFile($this->assetsDir.'/'.$this->assetFolder.'/'.$asset->file);
        
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
    
    public function generateURL(Asset $asset, PhingFile $file)
    {
        //Build modified param to help with caching
        $modtime   = $file->lastModified();
        $mod_param = ($modtime !== 0) ? "?$modtime" : '';
        
        //Build path to actual file
        $folder = (!empty($this->assetFolder)) ? $this->assetFolder : '';
        $url_path = $folder.$asset->file;
        
        return sprintf("http://%s%s%s", $this->url, $url_path, $mod_param);
    }
}

?>