<?php

use BapCat\Collection\ReadOnlyCollection;
use BapCat\Request\InvalidStateException;
use BapCat\Request\Request;
use BapCat\Values\HttpMethod;

class RequestTest extends PHPUnit_Framework_TestCase {
  public function setUp() {
    $this->method  = HttpMethod::POST();
    $this->uri     = '/test';
    $this->host    = 'example.com';
    $this->headers = [100];
    $this->query   = [10];
    $this->input   = [1];
    
    $this->request = new Request($this->method, $this->uri, $this->host, $this->headers, $this->query, $this->input);
  }
  
  public function testAccessors() {
    $this->assertSame($this->method, $this->request->method);
    $this->assertSame($this->uri,    $this->request->uri);
    $this->assertSame($this->host,   $this->request->host);
    
    $this->assertInstanceOf(ReadOnlyCollection::class, $this->request->headers);
    $this->assertInstanceOf(ReadOnlyCollection::class, $this->request->query);
    $this->assertInstanceOf(ReadOnlyCollection::class, $this->request->input);
    
    $this->assertSame($this->headers[0], $this->request->headers->get(0));
    $this->assertSame($this->query[0],   $this->request->query->get(0));
    $this->assertSame($this->input[0],   $this->request->input->get(0));
  }
  
  public function testFromGlobalsFailsInCli() {
    $this->setExpectedException(InvalidStateException::class);
    
    $request = Request::fromGlobals();
  }
  
  public function testIsJson() {
    $this->assertFalse($this->request->is_json);
    
    $request = new Request(HttpMethod::POST(), '/test', 'example.com', ['Accept' => 'application/ajax']);
    
    $this->assertTrue($request->is_json);
  }
}