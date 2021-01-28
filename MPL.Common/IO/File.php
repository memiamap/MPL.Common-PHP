<?php
declare(strict_types=1);

namespace MPL\Common\IO
{
  use MPL\Common\RelativeMapping;

  class File
  {
    // Private functions
    private static function MapLocalFileName(string $fileName): string {
    	if(isset($_SERVER['APPL_PHYSICAL_PATH']))
    		$returnValue = $_SERVER['APPL_PHYSICAL_PATH'] . $fileName;
    	else if(isset($_SERVER['DOCUMENT_ROOT']))
    		$returnValue = $_SERVER['DOCUMENT_ROOT'] . '/' . $fileName;
    	else
    	  $returnValue = $fileName;
    	  
    	return $returnValue;
    }
    
    // Public functions
    public static function LoadText(string $fileName, bool $raiseExceptionForEmptyFile = true): ?string {
      // Get the filename
      $contentFileName = self::MapLocalFileName($fileName);
    	  
    	// Verify the file exists
    	if (file_exists($contentFileName)) {
    	  $returnValue = file_get_contents($contentFileName);
    	  
    	  // Check for empty file
      	if ($raiseExceptionForEmptyFile && (!$returnValue || strlen($returnValue) == 0)) {
      	  throw new \Exception("The file '{$fileName}' is empty");
      	}
    	} else {
    	  throw new \Exception("The file '{$fileName}' does not exist");
    	}

      return $returnValue;
    }

    public static function LoadTextRelative(string $relativeFileName, bool $raiseExceptionForEmptyFile = true): ?string {
      // Get the filename
      $contentFileName = RelativeMapping::MapRelativePath($relativeFileName);
    	  
    	// Verify the file exists
    	if (file_exists($contentFileName)) {
    	  $returnValue = file_get_contents($contentFileName);
    	  
    	  // Check for empty file
      	if ($raiseExceptionForEmptyFile && (!$returnValue || strlen($returnValue) == 0)) {
      	  throw new \Exception("The file '{$fileName}' is empty");
      	}
    	} else {
    	  throw new \Exception("The file '{$fileName}' does not exist");
    	}

      return $returnValue;
    }
  }
}
?>