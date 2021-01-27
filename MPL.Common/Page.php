<?php
declare(strict_types=1);

namespace MPL\Common
{
  class Page
  {
    // Public functions
    // Returns the canonical URL for the current page.
    public static function GetCanonicalUrl(): string {
	    return Website::$BaseLocation . str_replace(RelativeMapping::GetSiteRelativeUrlSegment(), '', strtok($_SERVER["REQUEST_URI"], '?'));
    }

    // Returns the running page URL including sub-folders.
    public static function GetRunningPageUrl(): string {
      // Get the running file and strip the app path from it		
    	$returnValue = $_SERVER['SCRIPT_NAME'];
    	$returnValue = trim(str_replace(RelativeMapping::GetSiteRelativeUrlSegment(), '', $returnValue), '/');

    	return $returnValue;
    }
  }
}
?>