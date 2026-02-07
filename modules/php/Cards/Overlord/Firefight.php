<?php
namespace M44\Cards\Overlord;

class Firefight extends \M44\Cards\Standard\Firefight
{
  public function __construct($row)
  {
    parent::__construct($row);
    unset($this->text[2]);
  }

  public function getPlayableSubSections($side = null)
    {
        return [0,1,2,3,4,5];
    }
}
