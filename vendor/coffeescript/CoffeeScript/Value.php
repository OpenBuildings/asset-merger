<?php

namespace CoffeeScript;

Init::initialize();

class Value
{
  function __construct($v)
  {
    $this->v = $v;
  }

  function __toString()
  {
    return $this->v;
  }
}

?>
