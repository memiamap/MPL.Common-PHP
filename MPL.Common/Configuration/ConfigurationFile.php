<?php
declare(strict_types=1);

namespace MPL\Common\Configuration
{
  use MPL\Common\Conversion as Conversion;
  use MPL\Common\Collections as Collections;
  use MPL\Common\IO\File as File;

  class ConfigurationFile extends Collections\ListBase implements IConfigurationFile
  {
    // Constructors
    private function __construct() {
    }
    
    // Public functions
    public function GetSetting(string $setting) {
      if (!$this->TryGetSetting($setting, $returnValue)) {
        throw new \Exception("The setting $setting does not exist");
      }
      
      return $returnValue;
    }

    public function GetSettingFloat(string $setting): float {
      if (!$this->TryGetSettingFloat($setting, $returnValue)) {
        throw new \Exception("The setting $setting does not exist or is not a valid float");
      }
      
      return $returnValue;
    }

    public function GetSettingInteger(string $setting): int {
      if (!$this->TryGetSettingInteger($setting, $returnValue)) {
        throw new \Exception("The setting $setting does not exist or is not a valid integer");
      }
      
      return $returnValue;
    }

    public function GetSettingString(string $setting): string {
      if (!$this->TryGetSettingString($setting, $returnValue)) {
        throw new \Exception("The setting $setting does not exist or is not a valid string");
      }
      
      return $returnValue;
    }

    public function HasSetting(string $setting): bool {
      return $this->ContainsInternal($setting);
    }

    public static function LoadFromFile(string $fileName): ConfigurationFile {
    	$settingsData = File::LoadText($fileName);
      return self::LoadFromData($settingsData);
    }
    
    public static function LoadFromData(string $data): ConfigurationFile {
      $returnValue = new ConfigurationFile();
    	foreach (explode("\n", $data) as $setting) {
    		$parts = explode('||', $setting);
    		if (count($parts) >= 2) {
    		  $returnValue->AddInternal($parts[0], $parts[1]);
    		}   	  
    	}      

      return $returnValue;
    }
    
    public function TryGetSetting(string $setting, &$value): bool {
      $returnValue = false;

      if ($this->HasSetting($setting)) {
        $value = $this->GetInternal($setting);
        $returnValue = true;
      }

      return $returnValue;
    }

    public function TryGetSettingFloat(string $setting, ?float &$value): bool {
      $returnValue = false;
      
      if ($this->TryGetSetting($setting, $rawValue)) {
        if (Conversion::TryParseFloat($rawValue, $value)) {
          $returnValue = true;
        }
      }
      
      return $returnValue;
    }

    public function TryGetSettingInteger(string $setting, ?int &$value): bool {
      $returnValue = false;
      
      if ($this->TryGetSetting($setting, $rawValue)) {
        if (Conversion::TryParseInteger($rawValue, $value)) {
          $returnValue = true;
        }
      }
      
      return $returnValue;
    }

    public function TryGetSettingString(string $setting, ?string &$value): bool {
      $returnValue = false;
      
      if ($this->TryGetSetting($setting, $rawValue)) {
        if (Conversion::TryParseString($rawValue, $value)) {
          $returnValue = true;
        }
      }
      
      return $returnValue;
    }
  }
}
?>