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
    
    public static function GeneratePageRedirectMeta(string $target, int $delaySeconds = 30): string {
      return "<meta http-equiv=\"refresh\" content=\"{$delaySeconds}; url={$target}\" />";
    }

    public static function GeneratePageRedirectJS(string $target, int $delaySeconds = 30): string {
      $delayMS = $delaySeconds * 1000;
      return "<script>setInterval(function() { window.location.href = \"{$target}\"; }, {$delayMS})</script>";
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