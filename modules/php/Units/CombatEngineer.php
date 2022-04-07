<?php
namespace M44\Units;

class CombatEngineer extends Infantry
{
  public function __construct($row)
  {
    parent::__construct($row);
    $this->name = clienttranslate('Combat Engineer');
    $this->number = 4;
    $this->ignoreDefenseOnCloseAssault = true;
    $this->canBattleAndRemoveWire = true;
    $this->mustSweep = true;
    $this->desc = [
      clienttranslate('Ignore all terrain Battle dice reductions in close Assault'),
      clienttranslate('In wire may battle out at -1 die and still remove the wire'),
      clienttranslate('In minefield must clear the mines, instead of battling'),
      clienttranslate('If ordered on Infantry Assault, may move 2 hexes and remove wire or clear mines'),
    ];
    $this->applyPropertiesModifiers();
  }
}
