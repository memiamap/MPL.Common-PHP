<?php
declare(strict_types=1);

namespace MPL\Common
{
  use MPL\Common\Configuration\PageConfigurationBase;

  abstract class RequestPage
  {
    // Declarations
    protected ?PageConfigurationBase $pageConfiguration;

    // Constructors
    public function __construct() {
      $this->pageConfiguration = null;
    }

    // Protected functions
    protected function onGetPageHasFooter(): bool {
      return false;
    }

    protected function onGetPageHasHeader(): bool {
      return false;
    }

    // Public functions
    public function GetPageConfiguration(): ?PageConfigurationBase {
      return $this->pageConfiguration;
    }
    
    public function GetPageHasFooter(): bool {
      return $this->onGetPageHasFooter();
    }

    public function GetPageHasHeader(): bool {
      return $this->onGetPageHasHeader();
    }

    public abstract function RenderOutput(): void;

    public function SetPageConfiguration(?PageConfigurationBase $pageConfiguration): void {
      $this->pageConfiguration = $pageConfiguration;
    }
  }
}
?>