<?php
declare(strict_types=1);

namespace MPL\Common
{
  abstract class ControlBase
  {
    // Declarations
    protected ?RequestPage $parent;

    // Public functions
    public abstract function GetOutput(): string;

    public function RenderOutput(): void {
      echo $this->GetOutput();
    }
    
    public final function SetParent(RequestPage $parent): void {
      $this->parent = $parent;
    }
  }
}
?>