<?php
declare(strict_types=1);

namespace MPL\Common\Collections
{
  abstract class ListBase implements \Iterator
  {
    // Decalarations
    private $list = array();
    private $position = 0;

    // Protected methods
    protected function AddInternal(string $key, $item): void {
      if (!$this->ContainsInternal($key)) {
        $this->list[$key] = $item;
        $this->keys = array_keys($this->list);
      } else {
        throw new \Exception("An item with the key $key already exists");
      }
    }

    protected function ContainsInternal(string $key): bool {
      return array_key_exists($key, $this->list);
    }

    protected function GetInternal(string $key) {
      $returnValue = null;
      
      if ($this->ContainsInternal($key)) {
        $returnValue = $this->list[$key];
      }
      
      return $returnValue;
    }
    
    protected function GetKeys(): array {
      return array_keys($this->list);
    }

    // Public methods
    public function Count() {
      return count($this->list);
    }
    
    public function rewind() {
      $this->position = 0;
    }

    public function current() {
      return $this->list[$this->keys[$this->position]];
    }

    public function key() {
      return $this->keys[$this->position];
    }

    public function next() {
      ++$this->position;
    }

    public function valid() {
      return isset($this->keys[$this->position]);
    }
  }
}
?>