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
    protected bool $hasPageConfiguration;
    
    // Constructors
    public function __construct() {
      $this->controlRequestHandler = null;
      $this->pageConfiguration = null;
      $this->hasPageConfiguration = false;
    }

    // Protected functions
    protected function onExecutePage(): void { }

    protected function onGetOutput(): string {
      return null;
    }

    protected function onGetPageHasFooter(): bool {
      return false;
    }

    protected function onGetPageHasHeader(): bool {
      return false;
    }

    protected function onGetPageHasOutput(): bool {
      return false;
    }

    // Public functions
    public final function ExecutePage(): void {
      $this->onExecutePage();
    }

    public final function GetControlRequestHandler(): ?ControlRequestHandler {
      return $this->controlRequestHandler;
    }

    public final function GetOutput(): string {
      return $this->onGetOutput();
    }

    public final function GetPageConfiguration(): ?PageConfigurationBase {
      return $this->pageConfiguration;
    }
    
    public final function GetPageHasFooter(): bool {
      return $this->onGetPageHasFooter();
    }

    public final function GetPageHasHeader(): bool {
      return $this->onGetPageHasHeader();
    }

    public final function GetPageHasOutput(): bool {
      return $this->onGetPageHasOutput();
    }
    
    public final function RenderOutput(): void {
      if ($this->GetPageHasOutput()) {
        echo $this->GetOutput();
      }
    }

    public final function SetControlRequestHandler(?ControlRequestHandler $controlRequestHandler): void {
      $this->controlRequestHandler = $controlRequestHandler;
    }

    public final function SetPageConfiguration(?PageConfigurationBase $pageConfiguration): void {
      $this->pageConfiguration = $pageConfiguration;
      $this->hasPageConfiguration = ($pageConfiguration != null);
    }
  }
}
?>