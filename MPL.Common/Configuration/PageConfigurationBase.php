<?php

declare(strict_types=1);

namespace MPL\Common\Configuration
{
  abstract class PageConfigurationBase
  {
    // Declarations
  	public $ID;

  	// Constructors
  	public function __construct(?array $data) {
  	  // Check data is valid
  		if ($data && is_array($data)) {
        $dataSize = count($data);
        
        // Verify minimum size of configuration data
        $minSize = $this->getMinimumSize();
        if ($dataSize >= $minSize) {
          // Verify maximum size of configuration data
          $maxSize = $this->getMaximumSize();          
          if ($maxSize == 0 || $dataSize <= $maxSize) {
            $this->ID = $data[$this->getIdIndex()];
            
            // Parse remaining data in derived class
            if (!$this->parsePageConfiguration($data)) {
              throw new \Exception("The specified data for '{$this->ID}' could not be parsed");
            }
          } else {
            throw new \Exception("The length of the specified data ({$dataSize}) is above the maximum of {$maxSize}");
          }
        } else {
          throw new \Exception("The length of the specified data ({$dataSize}) is below the minimum of {$minSize}");
        }
  		} else {
  		  throw new \Exception('The specified data is not valid');
  		}
  	}
  	
  	// Protected functions
    protected abstract function getIdIndex(): int;

    protected function getMaximumSize(): int {
      return 0;
    }
    
    protected abstract function getMinimumSize(): int;

    protected abstract function parsePageConfiguration(array $data): bool;
  }
}
?>