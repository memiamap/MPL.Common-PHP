<?php
declare(strict_types=1);

namespace MPL\Common
{
  abstract class SessionManager
  {
    // Protected functions
  	protected function getSessionValue(string $name) {
  	  return $this->hasSessionValue($name) ? $_SESSION[$name] : null;
  	}

    protected function hasSessionValue(string $name): bool {
      return isset($_SESSION[$name]);
    }

  	protected function setSessionValue(string $name, $value): void {
  	  $_SESSION[$name] = $value;
  	}
  }
}
?>