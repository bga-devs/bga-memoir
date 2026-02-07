<?php
namespace M44\Models;
use M44\Managers\Players;
use M44\Managers\Units;
use M44\Helpers\Collection;
use M44\Core\Globals;
use M44\States\TacticCardTrait;
use M44\Board;
use M44\Managers\Terrains;
use M44\Scenario;

class SectionCard extends Card
{
  protected $subtitles = [];
  protected $texts = [];
  protected $sections = [];
  protected $nUnits = null;
  protected $nUnitsOnTheMove = null;
  protected $orderUnitsTitles = [];

  public function getSubtitle()
  {
    return $this->subtitles[$this->value] ?? $this->subtitle;
  }

  public function getText()
  {
    return $this->texts[$this->value] ?? $this->text;
  }

  public function getNotifString()
  {
    $flanks = [
      0 => \clienttranslate('the left flank'),
      1 => \clienttranslate('the center'),
      2 => \clienttranslate('the right flank'),
    ];
    return $flanks[$this->value];
  }


  public function getOrderUnitsTitle($val, $marineCommand)
  {
    return $this->orderUnitsTitles[$val] ?? '';
  }

  public function getSections()
  {
    $sections = [0, 0, 0];
    if ($this->nUnits != null && empty($this->sections)) {
      $sections[$this->value] = $this->nUnits;
    } else {
      $sections = $this->sections;
    }

    return $this->isCounterAttackMirror ? array_reverse($sections) : $sections;
  }

  public function getArgsOrderUnits()
  {
    $player = $this->getPlayer();
    $marineCommand = $player->isMarineCommand();

    $units = new Collection();
    $sectionId = $this->getExtraDatas('section');
    if ($this->isCounterAttackMirror) {
      $sectionId = $this->mirrorSection($sectionId);
    }
    
    if ($sectionId != null) {
      if ($n > 0 || $this->nUnitsOnTheMove > 0) {
        // Filter units that cannot be activated before turn 'until turn' 
        $unitstmp = $player->getUnitsInSection($sectionId);
        $unitstmp = $unitstmp->filter(function ($unit) {
          return (!($unit -> getExtraDatas('cannotBeActivatedUntilTurn') >= Globals::getTurn()));
        });        
      $units = $units->merge($unitstmp->getPositions());
      } 
    } else {
      foreach ($this->getSections() as $i => $n) {
        if ($n > 0 || $this->nUnitsOnTheMove > 0) {
          // Filter units that cannot be activated before turn 'until turn' 
          $unitstmp = $player->getUnitsInSection($i);
          $unitstmp = $unitstmp->filter(function ($unit) {
            return (!($unit -> getExtraDatas('cannotBeActivatedUntilTurn') >= Globals::getTurn()));
        });
       
        $units = $units->merge($unitstmp->getPositions());
        }
      }
    }

    $val = $this->isCounterAttackMirror ? 2 - $this->value : $this->value;
    $nbUnits = $marineCommand ? $this->nUnits + 1 : $this->nUnits;
    $nbOnTheMove =
      $this->nUnitsOnTheMove == 0 ? 0 : ($marineCommand ? $this->nUnitsOnTheMove + 1 : $this->nUnitsOnTheMove);
    return [
      'i18n' => ['desc'],
      'n' => $nbUnits,
      'nTitle' => $nbUnits,
      'nOnTheMove' => $nbOnTheMove,
      'desc' => $this->getOrderUnitsTitle($val, $marineCommand),
      'sections' => $this->getSections(),
      'units' => $units,
      'marineCommand' => $marineCommand,
    ];
  }

  public function nextStateAfterPlay()
  {
   if ($this->getExtraDatas('canblowbridge') === true) {
      return 'blowbridgeopt2'; // 'blowbridgeopt2'
    } elseif ($this->getExtraDatas('canArmorBreakthrough') === true) {
      return 'armorBreakthrough';
    } else {
      return parent::nextStateAfterPlay();
    }
  }

  public function canBlowBridge()
  { // TO DO filter cards that contain sections with blowable bridges
    $blowbridge = Globals::getBlowBridgeOpt2();
    return !is_null($blowbridge) && $blowbridge['side'] == Globals::getTeamTurn()
    && !empty(self::blowBridgeFilters($this)['terrains']);
  }

  public function blowBridgeFilters($card) 
  {
    $cardsections = $card->getSections();
    $side = Globals::getTeamTurn();
    $terrains = Terrains::getAll();
    $bridges = $terrains->filter(function ($t) {
      return $t instanceof \M44\Terrains\Bridge
      || $t instanceof \M44\Terrains\RailroadBridge;
    });
    // filter based on behavior 'CAN_BE_BLOWN'
    if (!empty($bridges->toArray()))
    {
      $bridgesblowable = array_filter($bridges->toArray(), fn($t) => 
      Board::canBeBlownCell($t->getPos())
    );
    } else {
      $bridgesblowable = [];
    }
    
    // filter based on section of the card
    if (!empty($bridgesblowable))
    {
      $selectablebridges = array_filter($bridgesblowable, fn($t) => 
      TacticCardTrait::isTerrainInCardSections($t, $cardsections, $side)
    );
    } else {
      $selectablebridges = [];
    }
    
    // option filter based if a a unit of the active player is neighbour to the bridge
    $selectablebridges2 = [];
    if (!empty($selectablebridges)) 
    {
      $blowbridgeoptions = Globals::getBlowBridgeOpt2();
      if(isset($blowbridgeoptions['option']) && $blowbridgeoptions['option'] == 'NEED_NEIGHBOUR_UNIT') {
        $selectablebridges2 = array_filter($selectablebridges, function ($t) {
          return self::hasNeighbourAlliedUnit($t);
        });
      }
    }


    return  ['terrains' => $selectablebridges2];
  }
  

  public function hasNeighbourAlliedUnit ($terrain) {
    $isNeighbourAlliedUnit = false;
    $neighbours = array();
    $neighbours = Board::getNeighbours($terrain->getPos());
    if (isset($neighbours)) {
      foreach($neighbours as $c) {
        $cellunit = Board::getUnitInCell($c);
        if(!is_null($cellunit) && $cellunit->getTeam()->getId() == Globals::getTeamTurn()) {
          $isNeighbourAlliedUnit = true;
          return $isNeighbourAlliedUnit;
        }
      }
    } else {
        return $isNeighbourAlliedUnit;
    }
  }

  public function canArmorBreakthrough() 
  {
    return true;
  }

  public function getPlayableSubSections($side = null)
    {
      $sections = $this->getSections();
      $flipped = ($side == Scenario::getTopTeam());
      foreach ($sections as $i => $n) {
        if ($n > 0) {
          $subSections[] = $flipped ?  (2 - $i) *2 : $i * 2;
          $subSections[] = $flipped ?  (2 - $i) *2 + 1 : $i * 2 + 1;
        }
      }
      return $subSections;
    }

}
