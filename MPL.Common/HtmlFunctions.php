<?php
declare(strict_types=1);

namespace MPL\Common
{
  use MPL\Common\IO\File;
  
  class HtmlFunctions
  {
    // Public functions
    public static function GenerateCssLink(string $cssFilename): string {
  		return "<link rel=\"stylesheet\" href=\"{$cssFilename}\">";
    }
    
    public static function GenerateScriptTag(string $scriptFilename): string {
  		return "<script src=\"{$scriptFilename}\"></script>";
    }

    public static function GenerateSvgInlineFromFile(string $path, ?string $class = null, ?string $id = null): string {
    	$returnValue = File::LoadTextRelative($path);

      // Add the class element if specified	
    	if ($class) {
    		$returnValue = str_replace('<svg', '<svg class="' . $class . '"', $returnValue);
    	}

    	// Add the ID element if specified
    	if ($id) {
    		$returnValue = str_replace('<svg', '<svg id="' . $id . '"', $returnValue);
    	}

    	return $returnValue;
    }
  }
}
?>