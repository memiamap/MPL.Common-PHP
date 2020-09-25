<?php
declare(strict_types=1);

namespace MPL\Common\Configuration
{
  interface IConfigurationFile
  {
    // Public functions
    public function GetSetting(string $setting);

    public function GetSettingFloat(string $setting): float;

    public function GetSettingInteger(string $setting): int;

    public function GetSettingString(string $setting): string;

    public function HasSetting(string $setting): bool;
    
    public function TryGetSetting(string $setting, &$value): bool;

    public function TryGetSettingFloat(string $setting, ?float &$value): bool;

    public function TryGetSettingInteger(string $setting, ?int &$value): bool;

    public function TryGetSettingString(string $setting, ?string &$value): bool;
  }
}
?>