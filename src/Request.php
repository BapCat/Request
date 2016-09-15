<?php namespace BapCat\Request;

use BapCat\Collection\ReadOnlyCollection;
use BapCat\Propifier\PropifierTrait;
use BapCat\Values\HttpMethod;

/**
 * An HTTP request
 */
class Request {
  use PropifierTrait;
  
  /**
   * @var  HttpMethod  $method
   */
  private $method;
  
  /**
   * @var  string  $uri
   */
  private $uri;
  
  /**
   * @var  string  $host
   */
  private $host;
  
  /**
   * @var  ReadOnlyCollection  $headers
   */
  private $headers;
  
  /**
   * @var  ReadOnlyCollection  $cookie
   */
  private $cookie;
  
  /**
   * @var  ReadOnlyCollection  $query
   */
  private $query;
  
  /**
   * @var  ReadOnlyCollection  $input
   */
  private $input;
  
  /**
   * Returns a new instance of the Request object (or its subclass) from the current HTTP request
   * 
   * @return  Request
   */
  public static function fromGlobals() {
    if(php_sapi_name() === 'cli') {
      throw new InvalidStateException('Requests can not be instantiated from globals in CLI mode');
    }
    
    $method  = HttpMethod::memberByKey($_SERVER['REQUEST_METHOD'], false);
    $uri     = strtok($_SERVER['REQUEST_URI'], '?');
    $host    = $_SERVER['HTTP_HOST'];
    $headers = getallheaders();
    
    $request = new static();
    $request->set($method, $uri, $host, $headers, $_COOKIE, $_GET, $_POST);
    
    return $request;
  }
  
  /**
   * @param  HttpMethod             $method   GET, POST, etc.
   * @param  string                 $uri      The request's URI
   * @param  string                 $host     The request's hostname or IP
   * @param  array<string, string>  $headers  The request's headers
   * @param  array<string, string>  $cookie   The request's cookie keys and values
   * @param  array<string, string>  $query    The URI query parameters (`$_GET`)
   * @param  array<string, string>  $input    The input parameters (`$_POST`)
   */
  public function __construct(HttpMethod $method, $uri, $host, array $headers, array $cookie = [], array $query = [], array $input = []) {
    $this->set($method, $uri, $host, $headers, $cookie, $query, $input);
  }
  
  /**
   * NOTE: Requests are intended to be immutable - please use this only to construct new requests
   * 
   * @param  HttpMethod             $method   GET, POST, etc.
   * @param  string                 $uri      The request's URI
   * @param  string                 $host     The request's hostname or IP
   * @param  array<string, string>  $headers  The request's headers
   * @param  array<string, string>  $cookie   The request's cookie keys and values
   * @param  array<string, string>  $query    The URI query parameters (`$_GET`)
   * @param  array<string, string>  $input    The input parameters (`$_POST`)
   * 
   * @return  void
   */
  protected function set(HttpMethod $method, $uri, $host, array $headers, array $cookie = [], array $query = [], array $input = []) {
    $this->method  = $method;
    $this->uri     = $uri;
    $this->host    = $host;
    $this->headers = new ReadOnlyCollection(array_change_key_case($headers, CASE_LOWER));
    $this->cookie  = new ReadOnlyCollection($cookie);
    $this->query   = new ReadOnlyCollection($query);
    $this->input   = new ReadOnlyCollection($input);
  }
  
  /**
   * @return  HttpMethod
   */
  protected function getMethod() {
    return $this->method;
  }
  
  /**
   * @return  string
   */
  protected function getUri() {
    return $this->uri;
  }
  
  /**
   * @return  string
   */
  protected function getHost() {
    return $this->host;
  }
  
  /**
   * @return  bool
   */
  protected function getIsJson() {
    return $this->headers->get('accept', null) === 'application/json';
  }
  
  /**
   * @return  array<string, string>
   */
  protected function getHeaders() {
    return $this->headers;
  }
  
  /**
   * @return  array<string, string>
   */
  protected function getCookie() {
    return $this->cookie;
  }
  
  /**
   * @return  array<string, string>
   */
  protected function getQuery() {
    return $this->query;
  }
  
  /**
   * @return  array<string, string>
   */
  protected function getInput() {
    return $this->input;
  }
}
