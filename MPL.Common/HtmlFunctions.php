<?php
declare(strict_types=1);

namespace MPL\Common
{
  class HtmlFunctions
  {
    // Public functions
    public static function GenerateCssLink(string $cssFilename): string {
  		return "<link rel=\"stylesheet\" href=\"{$cssFilename}\">";
    }
    
    public static function GenerateScriptTag(string $scriptFilename): string {
  		return "<script src=\"{$scriptFilename}\"></script>";
    }
  }
}
?>