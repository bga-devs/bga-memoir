<?php

const CARD_AIR_POWER = 10;
const CARD_AMBUSH = 11;
const CARD_ARMOR_ASSAULT = 12; // TWO COPIES
const CARD_ARTILLERY_BOMBARD = 14;
const CARD_BARRAGE = 15;
const CARD_BEHIND_LINES = 16;
const CARD_CLOSE_ASSAULT = 17;
const CARD_COUNTER_ATTACK = 18; // TWO COPIES
const CARD_DIG_IN = 20;
const CARD_DIRECT_FROM_HQ = 21; // TWO COPIES
const CARD_FIREFIGHT = 23;
const CARD_INFANTRY_ASSAULT = 24; // TWO COPIES
const CARD_MEDICS = 26;
const CARD_MOVE_OUT = 27; // TWO COPIES
const CARD_FINEST_HOUR = 29;

const CARD_CLASSES = [
  CARD_AIR_POWER => 'AirPower',
  CARD_AMBUSH => 'Ambush',
  CARD_ARMOR_ASSAULT => 'ArmorAssault',
  CARD_ARTILLERY_BOMBARD => 'ArtilleryBombard',
  CARD_BARRAGE => 'Barrage',
  CARD_BEHIND_LINES => 'BehindEnnemyLines',
  CARD_CLOSE_ASSAULT => 'CloseAssault',
  CARD_COUNTER_ATTACK => 'CounterAttack',
  CARD_DIG_IN => 'DigIn',
  CARD_DIRECT_FROM_HQ => 'DirectFromHQ',
  CARD_FIREFIGHT => 'Firefight',
  CARD_INFANTRY_ASSAULT => 'InfantryAssault',
  CARD_MEDICS => 'MedicsAndMechanics',
  CARD_MOVE_OUT => 'MoveOut',
  CARD_FINEST_HOUR => 'FinestHour',
];

foreach(CARD_CLASSES as $type => $class){
  $fp = fopen("Cards/".$class.'.php', 'w');
fwrite($fp, "<?php
namespace M44\Cards\Breakthrough;

class ".$class." extends \M44\Cards\Standard
{
public function __construct(\$row){
parent::__construct(\$row);
\$this->type  = '".$type."';
\$this->name  = clienttranslate(\"".$class."\");
\$this->text = [
  clienttranslate(''),
  clienttranslate(''),
  clienttranslate(''),
  clienttranslate(''),
  clienttranslate(''),
];
");

fwrite($fp, "
}
}
");


fclose($fp);
}
