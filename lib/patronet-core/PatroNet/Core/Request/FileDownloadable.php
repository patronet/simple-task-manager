<?php

namespace PatroNet\Core\Request;


/**
 * File downloader
 */
class FileDownloadable implements Downloadable
{
    use DownloadableTrait;
    
    protected $file;
    protected $filename;
    protected $mimeType;
    
    /**
     * @param string $file
     * @param string|null $filename
     * @param string|null $mimeType
     */
    public function __construct($file, $filename=null, $mimeType=null)
    {
        $this->file = $file;
        
        if(!is_null($filename)) 
        {
            $this->filename = $filename;
        } else {
            $this->filename = basename($file);
        }
        
        if(!is_null($mimeType)) 
        {
            $this->mimeType = $mimeType;
        } else {
            $this->mimeType = $this->getMimeTypeByFile($file);
            
            if(is_null($this->mimeType))
            {
                $FileExtension = $this->getFileExtension($filename);
                
                $this->mimeType = $this->getMimeTypeByExtension($FileExtension);
            }
        }
    }
    
    /**
     * Downloads the file
     */
    public function download()
    {
        $error = false;
        
        if ( !file_exists($this->file) ) 
        {
            $error = true;
        }
        
        if(!$error) 
        {
            $this->flushHeaders($this->filename, $this->mimeType);

            readfile($this->file);
        } else {
            header( "Location: " . $_SERVER['HTTP_REFERER'] );
        }
        
        exit();
    }
}
