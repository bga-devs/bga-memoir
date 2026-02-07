<?php
namespace M44\Cards\Overlord;

class Barrage extends \M44\Cards\Standard\Barrage
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->text[] = clienttranslate(
      'Play this card at the start of the turn, before your Field Generals play any of their cards.'
    );
  }

  public function getPlayableSubSections($side = null)
    {
        return [6];
    }
}
