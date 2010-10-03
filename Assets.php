<?php

include 'AssetType.php';

class Assets extends Task
{
    private $cssFolder   = 'css';     //Name of CSS folder
    private $jsFolder    = 'js';      //Name of CSS folder
    private $imagesFolder = 'images'; //Name of CSS folder

    private $assetsDir;               //Path to assets directory
    private $host;                    //Host of generated URL
    private $subdomains = array();    //Subdomains to append to host

    private $assets = array();        //Array of AssetType objs
    
    private $asset_folder_name;       //Folder asset is stored in

    
    public function init(){}
    
    //Sets the host of the generated URL
    //Do not include a subdomain if generating sub domains
    //this includes www.
    public function setHost($host)
    {
        $host = str_replace("\\", "/", $host);
        $this->host = (substr($host, -1) != '/') ? $host.'/' : $host;
    }
    
    //Set the sub domains to auto generate URL with
    //If this is not set, then no subdomain is generated
    public function setSubDomains($sub)
    {
        if(strlen($sub) > 0)
        {
            $this->subdomains = explode(',', $sub);
        }
    }
    
    //Set the directory where the assets are stored
    //We use so that we can get info on the file
    public function setDir(PhingFile $dir)
    {
        if(!$dir->exists())
        {
            throw new BuildException("Can not find asset directory: ".$dir->getAbsolutePath(), $this->location);
        }
        else
        {
            $this->assetsDir = $dir->getAbsolutePath();
        }
    }
    
    //Get details on assets
    function createAsset()
    {
        $num = array_push($this->assets, new AssetType());
        return $this->assets[$num-1];
    }
    
    //Lets process those assets...baby!
    public function main()
    {
        if(empty($this->assets))
        {
            throw new BuildException("Make sure you use <asset /> to define your assets", $this->location);
        }
        
        //Loop over each asset
        foreach($this->assets as $asset)
        {
            $file = $this->checkExistsAndReadable($asset);
            
            $url  = $this->generateURL($asset, $file);
        
            //Set the generated URL to use in the buildfile
            $this->project->setProperty($asset->returnProperty, $url);
        }
    }
    
    //Check that the file exists in the asset directory structure
    //and check we can read it
    private function checkExistsAndReadable(AssetType $asset)
    {
        switch($asset->assetType)
        {
            case AssetType::ASSET_TYPE_CSS:    $folder = $this->cssFolder;
                                               break;
                                                
            case AssetType::ASSET_TYPE_JS:     $folder = $this->jsFolder;
                                               break;
                                                
            case AssetType::ASSET_TYPE_IMAGE:  $folder = $this->imagesFolder;
                                               break;
                                               
            default:                           $folder = '';
        }
        
        //Set folder type for asset
        $this->asset_folder_name = $folder;
        
        //Build path to file
        $file = new PhingFile($this->assetsDir.'/'.$folder.'/'.$asset->filePath);
        
        if(!$file->exists())
        {
            throw new BuildException("Asset file does not exist: ".$file->getPath(), $this->location);
        }
        
        if(!$file->canRead())
        {
            throw IOException("Unable to read asset file: ".$file->getPath());
        }
        
        return $file;
    }
    
    //Generate the URL
    private function generateURL(AssetType $asset, PhingFile $file)
    {
        //Build modified param to help with caching and clearing
        $modtime   = $file->lastModified();
        $mod_param = ($modtime !== 0) ? "?$modtime" : '';
        
        //Build subdomain if passed in
        if(empty($this->subdomains))
        {
            //So were not using subdomains
            $sub = '';
        }
        else
        {
            $rand = rand(0, (count($this->subdomains)-1));
            $sub  = $this->subdomains[$rand].'.';
        }
        
        //Build path to actual file
        $folder = (!empty($this->asset_folder_name)) ? $this->asset_folder_name.'/' : '';
        $url_path = $folder.$asset->filePath;
    
        //Return URL
        return sprintf("http://%s%s%s%s", $sub, $this->host, $url_path, $mod_param);
    }
}

?>