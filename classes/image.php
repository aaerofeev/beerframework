<?php
class Image {
    protected $_mFilename;
    protected $_mFilesave;
    protected $_mWidth;
    protected $_mHeight;
    protected $_mImage;
    
    const MASTER_WIDTH = 1;
    const MASTER_HEIGHT = 2;
    const MASTER_MANUAL = 3;
    const MASTER_AUTO = 4;
    const MASTER_BOX = 5;
    
    public function __construct($filename)
    {
        $this->_mFilename = $filename;
        $this->_mFilesave = $filename;
        list($this->_mWidth,$this->_mHeight,$type) = getimagesize($filename);
        $this->_mImage = imagecreatefromjpeg($this->_mFilename);
    }
    
    public function __destruct(){
        imagedestroy($this->_mImage);
    }

    public function setSavePath($fileSavePath)
    {
        $this->_mFilesave = $fileSavePath;
    }
    
    public function crop($width,$height,$offsetX = 0,$offsetY = 0){
        $image = imagecreatetruecolor($width,$height);
        imagecopyresampled($image,$this->_mImage,0,0,$offsetX,$offsetY,$width,$height,$width,$height); 
        
        imagedestroy($this->_mImage);
        $this->_mImage = $image;
        imagejpeg($image,$this->_mFilesave,100);
        
        return $this;
    }
    
    public function resize($width = NULL, $height = NULL, $master = self::MASTER_MANUAL)
    {
        if($master == self::MASTER_BOX){
            if($this->_mWidth > $this->_mHeight){                
                $master = self::MASTER_HEIGHT;
            }else {                
                $master = self::MASTER_WIDTH;
            }
        }elseif($master == self::MASTER_AUTO){
            if($width > $height){
                $master = self::MASTER_WIDTH;
            }else $master = self::MASTER_HEIGHT;    
        }
        
        if($master == self::MASTER_WIDTH){
            $height = $this->_mHeight / ($this->_mWidth / $width);
        }elseif($master == self::MASTER_HEIGHT){
            $width = $this->_mWidth / ($this->_mHeight / $height);
        }    
        
        $image = imagecreatetruecolor($width,$height);
        imagecopyresampled($image,$this->_mImage,0,0,0,0,$width,$height,$this->_mWidth,$this->_mHeight);
        
        imagedestroy($this->_mImage);
        $this->_mImage = $image;
        imagejpeg($image,$this->_mFilesave,100);
        
        return $this;
    }
}
