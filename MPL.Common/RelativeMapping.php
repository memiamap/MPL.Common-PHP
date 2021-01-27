<?php
declare(strict_types=1);

namespace MPL\Common
{
  use MPL\Common\ErrorHandling;

  class RelativeMapping
  {
    // Private functions
    private static function mapRelativeFile(string $file): ?string {
      $returnValue = null;
      
    	if (isset($_SERVER['APPL_PHYSICAL_PATH'])) {
    		$returnValue = $_SERVER['APPL_PHYSICAL_PATH'] . $file;		
    	} else if (isset($_SERVER['URL'])) {
    		$levels = substr_count(str_replace(self::GetSiteRelativeUrlSegment(), '', $_SERVER['URL']), '/') - 1;
    		$returnValue = $file;
    		while ($levels-- > 0)
    			$returnValue = '../' . $returnValue;				
    	} else if (isset($_SERVER['DOCUMENT_ROOT'])) {
    		$returnValue = $_SERVER['DOCUMENT_ROOT'] . $file;
    	}
    	
    	return $returnValue;
    }

    // Public functions
    public static function GetSiteRelativeUrlSegment(): string {
      $returnValue = '';

    	if (isset($_SERVER['APPL_MD_PATH'])) {
    		$appPaths = explode('/', $_SERVER['APPL_MD_PATH']);
    		$i = count($appPaths);

    		while ($appPaths[--$i] != 'ROOT') {
    			$returnValue = '/' . $appPaths[$i] . $returnValue;
    		}
    	}

    	return $returnValue;
    }

    // Maps a relative to an absolute file path for the current site.
    public static function MapRelativePath(string $path, string $pathSeperator = '/'): string {
    	$path = str_replace($pathSeperator, DIRECTORY_SEPARATOR, $path);
    	// Append relative path
    	if (isset($_SERVER['APPL_PHYSICAL_PATH'])) {
    		$returnValue = str_replace('~' . DIRECTORY_SEPARATOR, $_SERVER['APPL_PHYSICAL_PATH'], $path);
    	} else {
    		$returnValue = str_replace('~' . DIRECTORY_SEPARATOR, $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR, $path);
    	}
    	
    	return $returnValue;
    }

    // Maps a relative to an absolute URL for the current site.
    public static function MapRelativeUrl(string $path): string {
    	// Append relative path
    	$returnValue = str_replace('~', Website::$BaseLocation, $path);
    	
    	return $returnValue;
    }

    public static function MapRelativeUrlWithTimestamp(string $path): string {
      $returnValue = null;

      // Get the physical path of the file and verify it exists
    	$physicalPath = self::MapRelativePath($path);
    	if (file_exists($physicalPath)) {
    	  $fileTime = filemtime($physicalPath);
      	$returnValue = self::MapRelativeUrl($path) . "?{$fileTime}";
    	} else {
    	  throw new \Exception("Cannot map physical path to '{$physicalPath}': File does not exist");
    	}
    	
    	return $returnValue;
    }

    public static function OutputRelativeUrl(string $path): void {
    	echo self::MapRelativeUrl($path);
    }

    public static function RelativeInclude(string $file, bool $includeOnce = true): void {
      $file = self::mapRelativeFile($file);
      if ($includeOnce) {
    	  include_once $file;
    	} else {
    	  include $file;
    	}
    }

    public static function RelativeIncludeToString(string $file): string {
      $file = self::mapRelativeFile($file);
      ob_start();
      include $file;
      $returnValue = ob_get_clean();
  	  return $returnValue;
    }

    public static function RelativeRequire(string $file, bool $requireOnce = true): void {
      $file = self::mapRelativeFile($file);
      if ($requireOnce) {
    	  require_once $file;
    	} else {
    	  require $file;
    	}
    }
  }
}
?>