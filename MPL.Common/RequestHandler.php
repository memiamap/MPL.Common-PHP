<?php
declare(strict_types=1);

namespace MPL\Common
{
  use MPL\Common\{ErrorHandling, RelativeMapping};
  use MPL\Common\Configuration\PageConfigurationManager;
  use MPL\Common\Reflection\{CallableFunctions, ReflectionFunctions, TypeCreator};

  class RequestHandler
  {
    // Constants
    private const LOCATION_404 = '404';
    private const LOCATION_PAGEFOOTER = 'PF';
    private const LOCATION_PAGEHEADER = 'PH';
    
    // Declarations
    private $baseLocation;
    private $locations = array();
    private $namespace;
    private $onCompletedRequestPageHandler;
    private $onLoadRequestPageHandler;
    private $onPostRenderRequestPageHandler;
    private $onPreRenderRequestPageHandler;
    private $pageConfigurationManager;

    // Constructors
    public function __construct(string $baseLocation, ?string $namespace = null) {
      $this->baseLocation = '~' . ltrim(rtrim($baseLocation, '\\/'), '~') . '/';
      $this->namespace = StringFunctions::SurroundWith($namespace, '\\');
    }

    // Private functions
    private function getClassName(string $path): string {
      $returnValue = basename($path, '.php');
      if ($this->namespace) {
        $returnValue = $this->namespace . $returnValue;
      }
      
      return $returnValue;
    }

    private function getRequestPageName(): string {
      $returnValue = null;
      
      if (isset($_SERVER['REQUEST_URI'])) {
        $page = '';
        
        foreach (explode('/', $_SERVER['REQUEST_URI']) as $part) {
          if (strlen($part) > 0) {
            if (strlen($page) > 0) $page .= '__';
            $page .= $part;
          }
        }
        
        $returnValue = RelativeMapping::MapRelativePath("{$this->baseLocation}{$page}.php");
      } else {
        ErrorHandling::LogMessage('Server REQUEST_URI index is missing');
        throw new \Exception('Unable to obtain requested page name');
      }
      
      return $returnValue;
    }

    private function hasLocation(string $locationName): bool {
      return isset($this->locations[$locationName]);
    }

    private function onCompletedRequestPage(RequestPage $page): void {
      $this->onRequestPageEventHandler($this->onCompletedRequestPageHandler, $page);
    }

    private function onLoadRequestPage(RequestPage $page): bool {
      $returnValue = true;
      
      if ($this->onLoadRequestPageHandler) {
        $returnValue = ($this->onLoadRequestPageHandler)($page);
      }
      
      return $returnValue;
    }

    private function onPostRenderRequestPage(RequestPage $page): void {
      $this->onRequestPageEventHandler($this->onPostRenderRequestPageHandler, $page);
    }

    private function onPreRenderRequestPage(RequestPage $page): void {
      $this->onRequestPageEventHandler($this->onPreRenderRequestPageHandler, $page);
    }

    private function onRequestPageEventHandler(?callable $eventHandler, RequestPage $page): void {
      if ($eventHandler) {
        ($eventHandler)($page);
      }      
    }
    
    private function serveLocation(string $locationName, ?RequestPage $associatedRequestPage = null): bool {
      $returnValue = false;
      
      // Try to get the location
      if ($this->tryGetLocation($locationName, $target)) {
        if ($this->tryLoadRequestPage($target, $locationPage)) {
          // Determine whether to use associated page configuration (if exists)
          if ($associatedRequestPage && !$locationPage->GetPageConfiguration()) {
            $locationPage->SetPageConfiguration($associatedRequestPage->GetPageConfiguration());
          }
          
          $locationPage->RenderOutput();
        } else {
          throw new \Exception("Unable to serve location '{$locationName}': Request page was not found");
        }
      } else {
        throw new \Exception("Unable to serve location: '{$locationName}' not found");
      }

      return $returnValue;
    }

    private function servePage(string $target): bool {
      $returnValue = false;
      
      // Try to load the request page
      if ($this->tryLoadRequestPage($target, $page)) {
        // Ensure that the page is loaded
        if ($this->onLoadRequestPage($page)) {
          
          // Serve the page header
          if ($page->GetPageHasFooter()) {
            $this->serveLocation(self::LOCATION_PAGEHEADER, $page);
          }

          // Serve the page
          $this->onPreRenderRequestPage($page);
          $page->RenderOutput();
          $this->onPostRenderRequestPage($page);

          // Serve the footer page
          if ($page->GetPageHasFooter()) {
            $this->serveLocation(self::LOCATION_PAGEFOOTER, $page);
          }
          
          // Mark as completed
          $returnValue = true;
          $this->onCompletedRequestPage($page);
        } else {
          // Page could not be served
          ErrorHandling::LogMessage("Could not serve '{$target}' due to failure from onLoadRequestPage");
        }
      } else {
        ErrorHandling::LogMessage("Could not load request page '{$target}'");
      }

      return $returnValue;
    }

    private function setLocation(string $locationName, string $relativeURL): bool {
      $returnValue = false;
      
      $page = RelativeMapping::MapRelativePath($relativeURL);
      if (file_exists($page)) {
        $this->locations[$locationName] = $page;
        $returnValue = true;
      }
      
      return $returnValue;
    }

    private function tryGetLocation(string $locationName, ?string &$target): bool {
      $returnValue = false;

      // Defaults
      $target = null;
      
      if ($this->hasLocation($locationName)) {
        $target = $this->locations[$locationName];
        $returnValue = true;
      }
      
      return $returnValue;
    }

    private function tryLoadRequestPage(string $target, ?RequestPage &$requestPage): bool {
      $returnValue = false;

      // Defaults
      $requestPage = null;

      try {
        // Try to createan instance of the page
        require $target;
        $page = TypeCreator::CreateType($target, $this->getClassName($target));
        if ($page) {
          // Verify the page is a valid type
          if ($page instanceof RequestPage) {
            $requestPage = $page;
            $returnValue = true;

            // Try to load the page configuration
            $this->pageConfigurationManager->TryLoadRequestPageConfiguration($requestPage);
          } else {
            throw new \Exception("The class '{$pageClass}' is not a type of RequestPage and cannot be executed");
          }
        } else {
          throw new \Exception("The class '{$pageClass}' does not exist in target file '{$target}'");
        }
      } catch (\Throwable $t) {
        ErrorHandling::LogThrowable($t, "Unable to serve requested page '{$target}'");
        throw new \Exception("Unable to serve requested page '{$target}'", 0, $t);
      }

      return $returnValue;
    }

    private function verifyCallableRequestPageHandler(?callable $callable, bool $throwExceptionOnInvalid = true): ?callable {
      $returnValue = null;
      
      // Verify callable parameter
      if ($callable) {
        if (CallableFunctions::HasParameterCount($callable, 1) &&
            ReflectionFunctions::IsTypeInheritedFrom(CallableFunctions::GetParameterType($callable, 0), RequestPage::class)) {
          $returnValue = $callable;
        } else if ($throwExceptionOnInvalid) {
          throw new \Exception("The specified callback is invalid");
        }
      }
      
      return $returnValue;
    }

    // Public functions
    public function HandleRequest(): bool {
      $returnValue = false;

      try {
        // Check the request page
        $pageName = $this->getRequestPageName();
        if (file_exists($pageName)) {
          // Serve the page
          $returnValue = $this->servePage($pageName);
        } else {
          // Try to serve a 404 page
          $returnValue = $this->serveLocation(self::LOCATION_404);
        }
      } catch (\Throwable $t) {
        ErrorHandling::LogThrowable($t, "Unable to handle requested resource");
        throw new \Exception("Unable to handle requested resource", 0, $t);
      }

      return $returnValue;
    }

    public function ServeStaticPage(string $relativeURL): void {
      $pageName = RelativeMapping::MapRelativePath($relativeURL);
      if (file_exists($pageName)) {
        $this->servePage($pageName, false);
      } else {
        throw new \Exception("The specified static page '$relativeURL' could not be served");
      }
    }

    public function Set404Page(string $relativeURL): void {
      if (!$this->setLocation(self::LOCATION_404, $relativeURL)) {
        throw new \Exception("The specified page '{$relativeURL}' does not exist");
      }
    }

    public function SetOnCompletedRequestPage(?callable $callable): void {
      $this->onCompletedRequestPageHandler = $this->verifyCallableRequestPageHandler($callable);
    }

    public function SetOnLoadRequestPage(?callable $callable): void {
      $this->onLoadRequestPageHandler = $this->verifyCallableRequestPageHandler($callable);
    }

    public function SetOnPostRenderRequestPage(?callable $callable): void {
      $this->onPostRenderRequestPageHandler = $this->verifyCallableRequestPageHandler($callable);
    }

    public function SetOnPreRenderRequestPage(?callable $callable): void {
      $this->onPreRenderRequestPageHandler = $this->verifyCallableRequestPageHandler($callable);
    }

    public function SetPageConfigurationManager(?PageConfigurationManager $pageConfigurationManager): void {
      $this->pageConfigurationManager = $pageConfigurationManager;
    }

    public function SetPageFooter(string $relativeURL): void {
      if (!$this->setLocation(self::LOCATION_PAGEFOOTER, $relativeURL)) {
        throw new \Exception("The specified page '{$relativeURL}' does not exist");
      }
    }

    public function SetPageHeader(string $relativeURL): void {
      if (!$this->setLocation(self::LOCATION_PAGEHEADER, $relativeURL)) {
        throw new \Exception("The specified page '{$relativeURL}' does not exist");
      }
    }
  }
}
?>