<?php

declare(strict_types=1);

namespace MPL\Common\Configuration
{
  use MPL\Common\RequestPage;
  use MPL\Common\IO\File;

  abstract class PageConfigurationManager
  {
    // Declarations
    private static $isLoaded = false;
    private static $pages;

  	// Private functions
  	private static function findPage(string $name, ?array &$config): bool {
  	  $returnValue = false;

      // Make sure configuration is loaded
      self::loadPageData();

      if (array_key_exists($name, self::$pages)) {
        $config = self::$pages[$name];
        $returnValue = true;
      }

      return $returnValue;
  	}
  	
  	private static function loadPageData(): void {
	    if (!self::$isLoaded) {
    		self::$pages = array();

	      // Load the configuration file
      	$siteData = File::LoadText('sitecontrol.config');
    		$siteParts = explode("\n", $siteData);

        foreach ($siteParts as $sitePart) {
          $parts = explode('||', $sitePart);
          if (count($parts) > 0) {
            self::$pages[$parts[0]] = $parts;
          }
        }

        self::$isLoaded = true;
      }
  	}
  	
  	// Protected functions
  	protected abstract function LoadPageConfigurationFromData(array $data): PageConfigurationBase;
  	
  	// Public functions
  	public function TryLoadPageConfigurationFromName(string $name, ?PageConfigurationBase &$pageConfiguration): bool {
  	  $returnValue = false;
  	  
  	  if (self::findPage($name, $config)) {
  	    $pageConfiguration = $this->LoadPageConfigurationFromData($config);
  	    $returnValue = true;
  	  }

  	  return $returnValue;
  	}

  	public function TryLoadRequestPageConfiguration(RequestPage $page): bool {
  	  $returnValue = false;
  	  
  	  if ($this->TryLoadPageConfigurationFromName(get_class($page), $pageConfiguration)) {
  	    $page->SetPageConfiguration($pageConfiguration);
  	    $returnValue = true;
  	  }
  	  
  	  return $returnValue;
  	}
  }
}
?>