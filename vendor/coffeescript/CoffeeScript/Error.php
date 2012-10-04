<?php

namespace CoffeeScript;

Init::initialize();

class Error extends \Exception
{
  function __construct($message)
  {
    $this->message = $message;
  }
}

?>
