<?php

require 'yuicompressor.php';
require 'CompressJS.php';
require 'CompressCSS.php';

class Compress extends DataType
{
    private $assetDir;
    private $tmpDir;
    private $paths;
    
    private $compress_js  = array();
    private $compress_css = array();
    
    public function __construct()
    {
        
    }
    
    public function setTmpDir(PhingFile $dir)
    {
        //Set tempory directory
        
        //TODO set some checking on the directory
        
        $this->tmpDir = $dir->getAbsolutePath();
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
    
    public function main($dir, $paths)
    {
        $this->assetDir = $dir;
        $this->paths = $paths;
        
        //Handle compression of JS first
        if(!empty($this->compress_js))
        {
            foreach($this->compress_js as $js)
            {
                //Build file path to yui-compressor
                $jar = new PhingFile('../lib/yuicompressor-2.4.2.jar');
                
                //Create instance of YUI compressor
                $yui = new YUICompressor($jar->getAbsolutePath(), $this->tmpDir);
                
                foreach($js->files as $file)
                {
                    //Build path to file
                    $file = new PhingFile($this->assetDir.'/js/'.$file->file);
                    
                    if($file->exists() && $file->canRead())
                    {
                        $yui->addFile($file->getAbsolutePath());
                    }
                    else
                    {
                        throw new BuildException("Unable to read asset file: ".$file->getPath(), $this->location);
                    }
                }
                
                $minified_js = $yui->compress();
                
                //Build path to output file
                $file = new PhingFile($this->assetDir.'/js/'.$js->file);
                
                file_put_contents($file->getAbsolutePath(), $minified_js);
            }
        }
    }
}

?>