<?php namespace BapCat\Request;

use BapCat\Collection\ReadOnlyCollection;
use BapCat\Propifier\PropifierTrait;
use BapCat\Values\HttpMethod;

class Request {
  use PropifierTrait;
  
  /**
   * @var HttpMethod
   */
  private $method;
  
  /**
   * @var string
   */
  private $uri;
  
  /**
   * @var string
   */
  private $host;
  
  /**
   * @var ReadOnlyCollection
   */
  private $headers;
  
  /**
   * @var ReadOnlyCollection
   */
  private $query;
  
  /**
   * @var ReadOnlyCollection
   */
  private $input;
  
  public static function fromGlobals() {
    if(php_sapi_name() === 'cli') {
      throw new InvalidStateException('Requests can not be instantiated from globals in CLI mode');
    }
    
    $method  = HttpMethod::memberByKey($_SERVER['REQUEST_METHOD'], false);
    $uri     = strtok($_SERVER['REQUEST_URI'], '?');
    $host    = $_SERVER['HTTP_HOST'];
    $headers = getallheaders();
    
    return new static($method, $url, $host, $headers, $_GET, $_POST);
  }
  
  public function __construct(HttpMethod $method, $uri, $host, array $headers, array $query = [], array $input = []) {
    $this->method  = $method;
    $this->uri     = $uri;
    $this->host    = $host;
    $this->headers = new ReadOnlyCollection(array_change_key_case($headers, CASE_LOWER));
    $this->query   = new ReadOnlyCollection($query);
    $this->input   = new ReadOnlyCollection($input);
  }
  
  protected function getMethod() {
    return $this->method;
  }
  
  protected function getUri() {
    return $this->uri;
  }
  
  protected function getHost() {
    return $this->host;
  }
  
  protected function getIsJson() {
    return $this->headers->get('accept', null) === 'application/json';
  }
  
  protected function getHeaders() {
    return $this->headers;
  }
  
  protected function getQuery() {
    return $this->query;
  }
  
  protected function getInput() {
    return $this->input;
  }
}
