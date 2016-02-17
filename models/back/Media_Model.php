<?php 
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root.'/Framework/Tools.php';

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm
{
	/**
	 * Save the file to the specified path
	 * @return boolean TRUE on success
	 */
	function save($path)
	{
		if(!move_uploaded_file($_FILES['myfile']['tmp_name'], $path))
		{
			return false;
		}

		return true;
	}

	function getName()
	{
		return $_FILES['myfile']['name'];
	}

	function getSize()
	{
		return $_FILES['myfile']['size'];
	}
}

class Media_Model 
{
	private $allowedExtensions 	= array();
	private $sizeLimit 			= 10485760;
	private $file;
	
	function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760)
    {        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        
//         $this->checkServerSettings();       

        if (isset($_FILES['myfile']['name'])) 
        {
            $this->file = new qqUploadedFileForm();
        } 
        else 
        {
            $this->file = false; 
        }
    }
    
    private function checkServerSettings()
    {
    	ini_set('max_execution_time', 1200);
    	ini_set('post_max_size', '10M');
    	ini_set('upload_max_filesize', '10M');
    
    	$postSize   = $this->toBytes(ini_get('post_max_size'));
    	$uploadSize = $this->toBytes(ini_get('upload_max_filesize'));
    
    	if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit)
    	{
    		$size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
    		die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
    	}
    }
    
    private function toBytes($str)
    {
    	$val    = trim($str);
    
    	$last   = strtolower($str[strlen($str)-1]);
    
    	switch ($last)
    	{
    		case 'g': $val *= 1024;
    		case 'm': $val *= 1024;
    		case 'k': $val *= 1024;
    	}
    
    	return $val;
    }
    
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $pre = '')
    {
    	if (!is_writable($uploadDirectory))
    	{
    		return array('error' => "Server error. Upload directory isn't writable.");
    	}
    
    	if (!$this->file)
    	{
    		return array('error' => 'No files were uploaded.');
    	}
    
    	$size = $this->file->getSize();
    
    	if ($size == 0)
    	{
    		return array('error' => 'File is empty');
    	}
    
    	if ($size > $this->sizeLimit)
    	{
    		return array('error' => 'File is too large');
    	}
    
    	$pathinfo   = pathinfo($this->file->getName());
    	$filename   = $pathinfo['filename'];
//     	//$filename = md5(uniqid());
    	$ext        = $pathinfo['extension'];
    
    	if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions))
    	{
    		$these = implode(', ', $this->allowedExtensions);
    		return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
    	}
    
    	$rand       =   Tools::getRandom(6);
    	$formedName =   $rand.'-'.$pre.'.'.$ext;
    	if ($this->file->save($uploadDirectory.$formedName))
    	{
    		chmod($uploadDirectory.$formedName, 0777);
    
    		if(file_exists($uploadDirectory.$formedName))
    		{
    			return array('success'=>true, 'fileName'=>$formedName);
    		}
    		else
    		{
    			return array('success'=>false);
    		}
    	}
    	else 
        {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
    }
    
    public function getThumb($name, $from, $to,  $value, $prop, $pre = '', $quality = 100)
    {
    	$original = $from.$name;
    	$to       = $to.$pre.$name;
    
    	$info     = getimagesize($original);
    
    	$width    = $info[0];
    	$height   = $info[1];
    	$type     = $info[2];
    
    	switch ($type)
    	{
    		case IMAGETYPE_JPEG:
    			$image = imagecreatefromjpeg($original);
    			break;
    				
    		case IMAGETYPE_GIF:
    			$image = imagecreatefromgif($original);
    			break;
    				
    		case IMAGETYPE_PNG:
    			$image = imagecreatefrompng($original);
    			break;
    	}
    
    	//---Determinar la propiedad a redimensionar y la propiedad opuesta
    	$prop_value 	= ($prop == 'width') ? $width : $height;
    	$prop_versus 	= ($prop == 'width') ? $height : $width;
    
    	//---Determinar el valor opuesto a la propiedad a redimensionar
    	$pcent 			= $value / $prop_value;
    	$value_versus 	= $prop_versus * $pcent;
    
    	//---Crear la imagen dependiendo de la propiedad a variar
    	$image_p 		= ($prop == 'width') ? imagecreatetruecolor($value, $value_versus) : imagecreatetruecolor($value_versus, $value);
    
    	//---Hacer una copia de la imagen dependiendo de la propiedad a variar
    	switch ($prop)
    	{
    		case 'width':
    			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $value, $value_versus, $width, $height);
    			break;
    
    		case 'height':
    			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $value_versus, $value, $width, $height);
    			break;
    	}
    
    	switch ($type)
    	{
    		case IMAGETYPE_JPEG:
    			imagejpeg($image_p, $to, $quality);
    			break;
    				
    		case IMAGETYPE_GIF:
    			imagegif($image_p, $to);
    			break;
    				
    		case IMAGETYPE_PNG:
    			$pngquality = floor(($quality - 10) / 10);
    			imagepng($image_p, $to, $pngquality);
    			break;
    	}
    
    	imagedestroy($image);
    
    	$newData = getimagesize($to);
    
    	$width    = $newData[0];
    	$height   = $newData[1];
    	$type     = $newData[2];
    
    	if ($newData)
    	{
    		chmod($to, 0777);
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    
    
    }
    
    public function cropImage($data, $dst_w, $dst_h, $source, $destination)
    {
    	echo $source;
    	//Quality
    	$quality   		= 100;
    	
    	//Source
    	$srcImage       = imagecreatefromjpeg($source);
    	
    	$sourceData 	= getimagesize($source);
    	$sourceWidth 	= $sourceData[0];
    	$sourceHeight	= $sourceData[1];
    	
    	$src_x = $data['x'] * $sourceWidth;
    	$src_y = $data['y'] * $sourceHeight;
    	
    	//ratio
    	$ratio = $sourceWidth / $dst_w;
    	
    	$src_w = $dst_w * $ratio;
    	$src_h = $dst_h * $ratio;
    	
    	
    	//Destination
    	$dstImage	= ImageCreateTrueColor( $dst_w, $dst_h);
    	
    	$dst_x = 0;
    	$dst_y = 0;
    
    	if (imagecopyresampled($dstImage, $srcImage, $dst_x, $dst_y, $src_x, $src_y,
    			$dst_w, $dst_h, $src_w, $src_h))
    	{
    		if (imagejpeg($dstImage, $destination, $quality))
    		{
    			chmod($destination, 0777);
    			return true;
    		}
    		else
    		{
    			return false;
    		}
    	}
    	else
    	{
    		return false;
    	}
    
    }
    
}
?>