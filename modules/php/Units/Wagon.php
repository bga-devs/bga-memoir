<?php
namespace M44\Units;

class Wagon extends Locomotive
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->type = WAGON;
  }
}
