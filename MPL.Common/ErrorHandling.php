<?php
declare(strict_types=1);

namespace MPL\Common
{
  class ErrorHandling
  {
    // Decalartions
    private static $displayErrorToConsole = false;
    private static $errorCallback;
    private static $hasRegisteredGlobalHandlers = false;

    // Private functions
    private static function onErrorCallback(bool $autoExit = true): void {
      try {
        if (self::$errorCallback) {
          (self::$errorCallback)();
        }
      } catch (\Throwable $t) {
        // Swallow this exception as nothing can be done with it for risk of a stack overflow
      }
      
      if ($autoExit) {
        exit();
      }
    }
    
    private static function globalErrorHandler(int $errno, string $errstr, ?string $errfile = null, ?int $errline = null): bool {
      $message = $errstr;
      if ($errfile && $errline) {
        $message .= ' at "' . $errfile . '":' . $errline;
      }
      self::LogMessage('Global Error Handler', $message);
      self::onErrorCallback();
    }
    
    private static function gloablExceptionHandler(?\Throwable $ex): void {
      if ($ex) {
        self::LogThrowable($ex, 'Global Exception Handler');
        if (self::$displayErrorToConsole) {
          $output = self::GetExceptionOutput($ex, true);
          echo $output;
        }
      }
      self::onErrorCallback();
    }
    
    private static function getOriginatorFunction(): string {
      $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
      $returnValue = 'Unknown';
      if ($backtrace && count($backtrace) > 0) {
        for ($i = 0; $i < count($backtrace); $i++) {
          $item = $backtrace[$i];
          if (isset($item['class']) && $item['class'] != 'MPL\Common\ErrorHandling') {
            $returnValue = $item['class'] . $item['type'] . $item['function'];
            break;
          }
        }
      }

      return $returnValue;
    }

    // Public functions
    public static function Dump($data, ?string $comment = null): void {
    	ob_start();
      var_dump($data);
      $contents = ob_get_contents();
      ob_end_clean();
      if ($comment) {
        self::LogMessage($comment, $contents); 
      } else {
        self::LogMessage($contents); 
      }
    }

    public static function DumpArray(?array $data, ?string $comment = null): void {
      if ($data && count($data) > 0) {
        self::Dump($data, $comment);
      } else if ($comment) {
        self::LogMessage($comment);
      }
    }

    public static function EnableErrorLogging(string $logFolder, bool $displayErrors = false): void {
    	ini_set('log_errors', '1');
    	ini_set('display_errors', $displayErrors ? '1' : '0');
      self::$displayErrorToConsole = $displayErrors;

    	$currentPath = str_ireplace('.php', '', strtolower(Page::GetRunningPageUrl()));
    	if ($currentPath != 'index') {
    		$logPath = '';
    		foreach (explode('/', $currentPath) as $part) {
    			if ($part != 'index')
    				$logPath .= $part . '_';
    		}
    		$logPath = trim($logPath, '_');
    	} else {
    		$logPath = $currentPath;
    	}
    	$logPath = RelativeMapping::MapRelativePath($logFolder . $logPath . '.log');
    	ini_set('error_log' , $logPath);
    }

    public static function GetExceptionOutput(\Throwable $ex, bool $recursive = false) : string {
      $count = 1;
      
      $returnValue = 'Exception caught by Global Exception Handler: ' . Environment::NewLine(2);
      
      while ($ex) {
        $returnValue .= $count . ':' . $ex->getMessage() . Environment::NewLine(2);
        $returnValue .= 'in ' . $ex->getFile() . ':' . $ex->getLine() . Environment::NewLine(2);
        $returnValue .= 'Stack Trace:' . Environment::NewLine(1);
        $returnValue .= str_replace("\n", Environment::NewLine(1), $ex->getTraceAsString()) . Environment::NewLine(2);
        $ex = $recursive ? $ex->getPrevious() : null;
        $count++;
      }

      return $returnValue;
    }
    
    public static function LogMessage(string $error, ?string $comment = null): void {
      $function = self::getOriginatorFunction();
      $comment = $comment ? ",$comment" : '';
      $comment = StringFunctions::RemoveLinebreaks($comment);
      $error = StringFunctions::RemoveLinebreaks($error);
      error_log("$function,$error$comment");
    }

    public static function LogThrowable(\Throwable $throwable, ?string $comment = null): void {
      $i = 0;
      $message = 'Error Raised:';

      $t = $throwable;
      while ($t) {
        if ($i > 0) $message .= '||';
        $message .= $i++ . '[' . $t->getMessage() . ' at "' . $t->getFile() . '":' . $t->getLine() .']';
        $t = $t->getPrevious();
      }

      if ($comment) {
        self::LogMessage($comment, $message);
      } else {
        self::LogMessage($message);
      }
    }

    public static function SetGlobalErrorHandler(?callable $callback = null): void {
      self::$errorCallback = $callback;
      if (!self::$hasRegisteredGlobalHandlers) {
        set_error_handler(function(int $errno, string $errstr, ?string $errfile = null, ?int $errline = null) { return self::globalErrorHandler($errno, $errstr, $errfile, $errline); });
        set_exception_handler(function(?\Throwable $ex) { self::gloablExceptionHandler($ex); });
        self::$hasRegisteredGlobalHandlers = true;
      }
    }
  }
}
?>