<?php
declare(strict_types=1);

namespace MPL\Common
{
  abstract class ControlBase
  {
    // Public functions
    public abstract function GetOutput(): string;

    public function RenderOutput(): void {
      echo $this->GetOutput();
    }
  }
}
?>