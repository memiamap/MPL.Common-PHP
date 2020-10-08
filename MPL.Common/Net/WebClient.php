<?php
declare(strict_types=1);

namespace MPL\Common\Net
{
  use MPL\Common\{Conversion, ErrorHandling};

  class WebClient
  {
    // Constants
    const AUTHTYPE_BASIC = 1;
    const AUTHTYPE_BEARER = 2;
    const AUTHTYPE_NONE = 0;
    
    const METHOD_GET = 0;
    const METHOD_POST = 1;
    
    // Declarations
    private static $userAgent;
    
    // Constructors
    public static function __init() {
      self::$userAgent = $_SERVER['HTTP_USER_AGENT'];
    }
    
    // Private functions
    private static function getAuthorisationText(int $authType, ?string $authCode, ?string &$output): bool {
      $returnValue = false;
      
      switch ($authType) {
        case WebClient::AUTHTYPE_BASIC:
          $output = "authorization: Basic $authCode";
          $returnValue = true;
          break;
        
        case WebClient::AUTHTYPE_BEARER:
          $output = "authorization: Bearer $authCode";
          $returnValue = true;
          break;

        case WebClient::AUTHTYPE_NONE:
          break;
         
        default:
          throw new \Exception("The specified authorisation type $authType was not recognised");
      }
      
      return $returnValue;
    }

    private static function getHeaders(int $method, int $authType, ?string $authCode = null): array {
      $returnValue = array();

  		if ($method == WebClient::METHOD_POST) {
  			$returnValue[] = 'POST * HTTP/1.0';
  			$returnValue[] = 'Content-type: application/x-www-form-urlencoded;charset="utf-8"';
  		} else if ($method == WebClient::METHOD_GET) {
  			$returnValue[] = 'GET * HTTP/1.0';
  			$returnValue[] = 'Content-type: text/xml;charset="utf-8"';			
  		}
  		$returnValue[] = "Accept: text/xml";
  		$returnValue[] = "Cache-Control: no-cache";
  		$returnValue[] = "Pragma: no-cache";
  		if (self::getAuthorisationText($authType, $authCode, $authText)) {
  		  $returnValue[] = $authText;
  		}

      return $returnValue;
    }

    private static function initialiseCurl(int $method, string $url, int $authType, ?string $authCode = null, ?string $data = null) {
 		  $returnValue = curl_init();
  	  curl_setopt($returnValue, CURLOPT_RETURNTRANSFER, 1);
  	  curl_setopt($returnValue, CURLOPT_TIMEOUT, 60);
  	  curl_setopt($returnValue, CURLOPT_USERAGENT, self::$userAgent);
  		curl_setopt($returnValue, CURLOPT_URL, $url);
  	  curl_setopt($returnValue, CURLOPT_HTTPHEADER, self::getHeaders($method, $authType, $authCode));
  		if ($method == WebClient::METHOD_POST) {
        curl_setopt($returnValue, CURLOPT_POST, 1);
        curl_setopt($returnValue, CURLOPT_POSTFIELDS, $data);			
      }

  		return $returnValue;
    }

  	private static function send(int $method, string $url, int $authType = WebClient::AUTHTYPE_NONE, ?string $authCode = null, ?string $data = null, ?string &$response): bool {
  	  $returnValue = false;
  	  
  	  // Verify authorisation
  	  if (self::verifyAuthType($authType, $authCode, $authHeader, $warning)) {
  	    // Verify method
  	    if (self::verifyMethod($method, $warning)) {
          try {
            // Configure and execute the request
        		$request = self::initialiseCurl($method, $url, $authType, $authCode, $data);
        		$curlResponse = curl_exec($request);
        		
        		// Process the response and tidy up
        		$httpStatus = curl_getinfo($request, CURLINFO_HTTP_CODE);
        		$error = curl_error($request);
        		curl_close($request);

        		// Check for success state
        		if ($httpStatus >= 200 && $httpStatus < 300) {
        		  Conversion::TryParseString($curlResponse, $response);
        			$returnValue = true;
        		} else {
        		  // Log the error
        		  $errorMessage = "Request URL '$url' resulted in HTTP status code $httpStatus";
        		  if ($error) {
        		    $errorMessage .= ". Error: $error";
        		  }
        		  
        		  if (Conversion::TryParseString($curlResponse, $data)) {
        			  ErrorHandling::LogMessage($errorMessage, $data);
        			} else {
        			  ErrorHandling::LogMessage($errorMessage);        			  
        			}
        		}
      		} catch (\Throwable $t) {
      		  ErrorHandling::LogThrowable($t, "Unable to execute curl request to '$url'");
      		}
  	    } else {
    	    ErrorHandling::LogMessage("Method was not verified: $warning");
    	    throw new \Exception('Method not valid');
  	    }
  	  } else {
  	    ErrorHandling::LogMessage("Authorisation type was not verified: $warning");
  	    throw new \Exception('Authorisation configuration not valid');
  	  }
  	  
  	  return $returnValue;
  	}

  	private static function verifyAuthType(int $authType, ?string $authCode, ?string &$authHeader, ?string &$warning): bool {
  	  $returnValue = false;
  	  
  	  if ($authType >= 0 && $authType <= 2) {
  	    // Check for authorisation type
  	    if ($authType == WebClient::AUTHTYPE_NONE) {
  	      // Check there is no auth code
  	      if (!$authCode) {
  	        $returnValue = true;
  	      } else {
  	        $warning = "Auth code '$authCode' specified in non-authorised mode";
  	      }
  	    } else {
  	      // Must have auth code
  	      if ($authCode && strlen($authCode) > 0) {
  	        $returnValue = true;
  	        if ($authType == WebClient::AUTHTYPE_BASIC) {
  	          $authHeader = "Authorization: Basic $authCode";
  	        } else {
  	          $authHeader = "Authorization: Bearer $authCode";
  	        }
  	      } else {
  	        $warning = 'Auth code is missing';
  	      }
  	    }
  	  } else {
  	    $warning = "Invalid authorisation type '$authType'";
  	  }
  	  
  	  return $returnValue;
  	}

  	private static function verifyMethod(int $method, ?string &$warning): bool {
  	  $returnValue = false;
  	  
  	  if ($method >= 0 && $method <= 1) {
  	    $returnValue = true;
  	  } else {
  	    $warning = "Method '$method' is invalid";
  	  }
  	  
  	  return $returnValue;
    }
  
    // Public functions
  	public static function GetString(string $url, int $authType = WebClient::AUTHTYPE_NONE, ?string $authCode = null): ?string {
  	  $returnValue = null;
  	  
      if (self::send(WebClient::METHOD_GET, $url, $authType, $authCode, null, $response)) {
        $returnValue = $response;
      } else {
  	    ErrorHandling::LogMessage("Failed to get string from '$url'");
      }
  	  
  	  return $returnValue;
  	}

    public static function PageExists(string $url): bool {
      $returnValue = false;

      try {
        // Configure and execute the request
    		$request = self::initialiseCurl(WebClient::METHOD_GET, $url, WebClient::AUTHTYPE_NONE);
    		curl_setopt($request, CURLOPT_NOBODY, true);
    		curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
    		$curlResponse = curl_exec($request);
    		
    		// Process the response and tidy up
    		$httpStatus = curl_getinfo($request, CURLINFO_HTTP_CODE);
    		curl_close($request);

    		// Check for success state
    		if ($httpStatus >= 200 && $httpStatus < 300) {
    			$returnValue = true;
    		}
  		} catch (\Throwable $t) {
  		  ErrorHandling::LogThrowable($t, "Unable to verify whether page '$url' exists");
  		}
  		
  		return $returnValue;
    }
    
  	public static function PostString(string $url, string $postData, int $authType = WebClient::AUTHTYPE_NONE, ?string $authCode = null): ?string {
  	  $returnValue = null;
  	  
      if (self::send(WebClient::METHOD_POST, $url, $authType, $authCode, $postData, $response)) {
        $returnValue = $response;
      } else {
  	    ErrorHandling::LogMessage("Failed to post string to '$url'");
      }
  	  
  	  return $returnValue;
  	}

    public static function SetUserAgent(string $userAgent): void {
      self::$userAgent = $userAgent;
    }
  }
  
  WebClient::__init();
}
?>