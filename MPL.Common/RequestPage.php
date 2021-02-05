<?php
declare(strict_types=1);

namespace MPL\Common
{
  use MPL\Common\Configuration\PageConfigurationBase;

  abstract class RequestPage
  {
    // Declarations
    protected ?ControlRequestHandler $controlRequestHandler;
    protected ?PageConfigurationBase $pageConfiguration;

    // Constructors
    public function __construct() {
      $this->controlRequestHandler = null;
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
    public function GetControlRequestHandler(): ?ControlRequestHandler {
      return $this->controlRequestHandler;
    }

    public abstract function GetOutput(): string;

    public function GetPageConfiguration(): ?PageConfigurationBase {
      return $this->pageConfiguration;
    }
    
    public function GetPageHasFooter(): bool {
      return $this->onGetPageHasFooter();
    }

    public function GetPageHasHeader(): bool {
      return $this->onGetPageHasHeader();
    }

    public function RenderOutput(): void {
      echo $this->GetOutput();
    }

    public function SetControlRequestHandler(?ControlRequestHandler $controlRequestHandler): void {
      $this->controlRequestHandler = $controlRequestHandler;
    }

    public function SetPageConfiguration(?PageConfigurationBase $pageConfiguration): void {
      $this->pageConfiguration = $pageConfiguration;
    }
  }
}
?>