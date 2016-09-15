<?php namespace BapCat\Request;

/**
 * An HTTP request created from the PHP globals
 */
class RequestFromGlobals extends Request {
  public function __construct() {
    $this->createFromGlobals();
  }
}
