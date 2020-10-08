<?php
declare(strict_types=1);

namespace MPL\Common
{
    use MPL\Common\ErrorHandling;
  class RelativeMapping
  {
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

    public static function OutputRelativeUrl(string $path): void {
    	echo self::MapRelativeUrl($path);
    }

    public static function RelativeInclude(string $file, bool $includeOnce = true): void {
    	if (isset($_SERVER['URL'])) {
    		$levels = substr_count(str_replace(self::GetSiteRelativeUrlSegment(), '', $_SERVER['URL']), '/') - 1;
    	} else {
    		$levels = substr_count(str_replace(self::GetSiteRelativeUrlSegment(), '', $_SERVER['SCRIPT_FILENAME']), '/') - 1;
    	}

  		while ($levels-- > 0) {
  			$file = '../' . $file;
  		}

      if ($includeOnce) {
    	  include_once $file;
    	} else {
    	  include $file;
    	}
    }

    public static function RelativeRequire(string $file, bool $requireOnce = true): void {
    	if (isset($_SERVER['APPL_PHYSICAL_PATH'])) {
    		$file = $_SERVER['APPL_PHYSICAL_PATH'] . $file;		
    	} else if (isset($_SERVER['URL'])) {
    		$levels = substr_count(str_replace(self::GetSiteRelativeUrlSegment(), '', $_SERVER['URL']), '/') - 1;
    		while ($levels-- > 0)
    			$file = '../' . $file;				
    	} else if (isset($_SERVER['DOCUMENT_ROOT'])) {
    		$file = $_SERVER['DOCUMENT_ROOT'] . $file;
    	}
      if ($requireOnce) {
    	  require_once $file;
    	} else {
    	  require $file;
    	}
    }
  }
}
?>