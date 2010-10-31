<?php

require_once 'yuicompressor.php';
require_once 'CompressJS.php';
require_once 'CompressCSS.php';

class Compress extends DataType
{
    private $assetDir;
    private $tmpDir;
    private $paths;
    
    private $yui;
    
    private $compress_js  = array();
    private $compress_css = array();
    
    public function __construct(){}
    
    public function setTmpDir(PhingFile $dir)
    {
        if(!$dir->exists())
        {
            $dir->mkdirs();
        }
        
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
    
    private function yuiInstance($is_css = FALSE)
    {
        $jar = new PhingFile('../lib/yuicompressor-2.4.2.jar');

        $instance = new YUICompressor($jar->getAbsolutePath(), $this->tmpDir);
        
        if($is_css)
        {
            $instance->setOption('type', 'css');
        }
        
        return $instance;
    }
    
    private function yuiAddFile($file, $is_css = FALSE)
    {
        if(!$is_css)
        {
            $folder = '/js/';
        }
        else
        {
            $folder = '/css/';
        }
        
        $add = new PhingFile($this->assetDir.$folder.$file->file);
        
        if($add->exists() && $add->canRead())
        {
            $this->yui->addFile($add->getAbsolutePath());
        }
        else
        {
            throw new BuildException("Unable to read asset file: ".$add->getPath(), $this->location);
        }
    }
    
    private function output($is_css, $data, $js)
    {
        if(!$is_css)
        {
            $folder = '/js/';
        }
        else
        {
            $folder = '/css/';
        }
        
        //Build path to output file
        $out = new PhingFile($this->assetDir.$folder.$js->file);
        
        //Check whether to overwrite files
        if($out->exists() && !$js->overwrite)
        {
            throw new BuildException("Trying to write to ".$js->file." but the overwrite flag has not been set to TRUE", $this->location);
        }
        else
        {
            if($out->exists())
            {
                $out->delete();
            }
        }
        
        //Write compressed JS to file
        file_put_contents($out->getAbsolutePath(), $data);
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
                $this->yui = $this->yuiInstance();
                
                foreach($js->files as $file)
                {
                    $this->yuiAddFile($file);
                }
                
                $minified_js = $this->yui->compress();
                
                $this->output(FALSE, $minified_js, $js);
            }
        }
        
        //Handle compression of CSS
        if(!empty($this->compress_css))
        {
            foreach($this->compress_css as $css)
            {
                $this->yui = $this->yuiInstance(TRUE);
                
                foreach($css->files as $file)
                {
                    $this->yuiAddFile($file, TRUE);
                }
                
                $minified_css = $this->yui->compress();
                
                $this->output(TRUE, $minified_css, $css);
            }
        }
    }
}

?>