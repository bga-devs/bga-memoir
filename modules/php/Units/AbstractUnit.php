<?php
namespace M44\Units;

use M44\Board;
use M44\Scenario;
use M44\Managers\Players;
use M44\Managers\Units;
use M44\Managers\Cards;
use M44\Managers\Tokens;
use M44\Managers\Teams;

class AbstractUnit extends \M44\Helpers\DB_Model implements \JsonSerializable
{
  protected $table = 'units';
  protected $primary = 'unit_id';
  protected $attributes = [
    'id' => ['unit_id', 'int'],
    'x' => ['x', 'int'],
    'y' => ['y', 'int'],
    'nation' => 'nation',
    'nUnits' => ['figures', 'int'],
    'badge' => ['badge', 'int'],
    'moves' => ['moves', 'int'],
    'fights' => ['fights', 'int'],
    'retreats' => ['retreats', 'int'],
    'grounds' => ['grounds', 'int'],
    'activationCard' => 'activation_card',
    'extraDatas' => ['extra_datas', 'obj'],
  ];

  protected $id = null;
  protected $x = null;
  protected $y = null;
  protected $sections = null;
  protected $nation = null;
  protected $nUnits = null;
  protected $badge = null;
  protected $datas = null;
  protected $activationCard = null;
  protected $moves = 0;
  protected $fights = 0;
  protected $grounds = 0;

  /*
   * STATIC INFORMATIONS
   */
  protected $staticAttributes = ['number', 'type', 'statName', 'name', 'desc'];
  protected $number = null;
  protected $type = null;
  protected $statName = null;
  protected $name = null;
  protected $desc = [];

  /*
   * UNIT PROPERTIES
   */
  protected $properties = [
    'maxUnits',
    'movementRadius',
    'movementAndAttackRadius',
    'attackPower',
    'mustSeeToAttack',
    'maxGrounds',
    'medalsWorth',
    'retreatHex',
    'ignoreCannotBattle',
    'canBattleAndRemoveWire',
    'ignoreDefenseOnCloseAssault',
    'ignoreDefense',
    'mustSweep', // must sweep the mines instead of attack
    'targets',
    'canOverrun',
    'canIgnoreOneFlag',
    'cannotBattleIfMoved',
    'mustIgnore1Flag', // Japanese
    'bonusCloseAssault', // Japanese
    'banzai', // can move up to 2 and still battle
    'maxMalus', // maximum malus for attack
  ];

  protected $attackPower = [];
  protected $mustSeeToAttack = true;
  protected $maxGrounds = 0;
  protected $medalsWorth = 1;
  protected $retreatHex = 1;
  protected $ignoreCannotBattle = false;
  protected $targets = [\INFANTRY => true, ARMOR => true, ARTILLERY => true];

  public function __construct($row)
  {
    if ($row != null) {
      parent::__construct($row);
      $this->sections = $row['sections'];
      $this->applyPropertiesModifiers();
    }
  }

  public function applyPropertiesModifiers()
  {
    $prop = $this->getExtraDatas('properties') ?? [];
    foreach ($prop as $name => $value) {
      $this->$name = $value;
    }
  }

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'x' => $this->x,
      'y' => $this->y,
      'sections' => $this->sections,

      'number' => $this->number,
      'type' => $this->type,
      'nation' => $this->nation,
      'name' => $this->name,
      'figures' => $this->nUnits,
      'badge' => $this->badge,
      'activationCard' => $this->activationCard,
      'onTheMove' => $this->datas['onTheMove'] ?? false,
    ];
  }

  public function getStaticUiData()
  {
    $t = array_merge($this->staticAttributes, $this->properties);
    $datas = [];
    foreach ($t as $prop) {
      if (isset($this->$prop)) {
        $datas[$prop] = $this->$prop;
      }
    }

    return $datas;
  }

  /////////////////////////////////////////
  //    ____      _   _
  //  / ___| ___| |_| |_ ___ _ __ ___
  // | |  _ / _ \ __| __/ _ \ '__/ __|
  // | |_| |  __/ |_| ||  __/ |  \__ \
  //  \____|\___|\__|\__\___|_|  |___/
  /////////////////////////////////////////

  public function getProperty($prop)
  {
    if (!in_array($prop, $this->properties)) {
      throw new \BgaVisibleSystemException('Trying to access a non existing unit property : ' . $prop);
    }

    return $this->$prop ?? null;
  }

  public function __call($method, $args)
  {
    if (!in_array($method, $this->properties)) {
      return parent::__call($method, $args);
    }

    return $this->getProperty($method);
  }

  public function getPos()
  {
    return [
      'x' => $this->x,
      'y' => $this->y,
    ];
  }

  public function getTeamId()
  {
    return in_array($this->nation, Units::$nations[AXIS]) ? AXIS : ALLIES;
  }
  public function getTeam()
  {
    return Teams::get($this->getTeamId());
  }
  public function getPlayer()
  {
    $section = $this->sections[0];
    return $this->getTeam()->getPlayerInCharge($section);
  }

  public function isOpponent($unit)
  {
    $allies = Units::$nations[ALLIES];
    $a = in_array($this->nation, $allies);
    $b = in_array($unit->getNation(), $allies);
    return ($a && !$b) || (!$a && $b);
  }

  public function getCampDirection()
  {
    // Useful for retreat
    return in_array($this->nation, Units::$nations[Scenario::getTopTeam()]) ? -1 : 1;
  }

  public function getActivationOCard()
  {
    return is_null($this->activationCard) ? null : Cards::get($this->activationCard);
  }

  public function getSections()
  {
    return $this->sections;
  }

  public function isWounded()
  {
    return $this->maxUnits != $this->nUnits;
  }

  public function getAttackModifier($cell)
  {
    return 0;
  }

  public function cannotBattle()
  {
    return $this->getExtraDatas('cannotBattle');
  }

  public function isCamouflaged()
  {
    return Tokens::getOnCoords('board', $this->getPos())
      ->filter(function ($t) {
        return $t['type'] == \TOKEN_CAMOUFLAGE;
      })
      ->count() != 0;
  }

  public function canTarget($unit)
  {
    return $this->targets[$unit->getType()] ?? true;
  }

  //////////////////////////////////////
  //    ___  ____  ____  _____ ____
  //   / _ \|  _ \|  _ \| ____|  _ \
  //  | | | | |_) | | | |  _| | |_) |
  //  | |_| |  _ <| |_| | |___|  _ <
  //   \___/|_| \_\____/|_____|_| \_\
  //////////////////////////////////////

  public function activate($card, $onTheMove = false)
  {
    $cardId = is_int($card) ? $card : $card->getId();
    $this->setActivationCard($cardId);
    $this->setExtraDatas('onTheMove', $onTheMove);
  }

  public function disable()
  {
    $this->setMoves(\INFINITY);
    $this->setFights(\INFINITY);
  }

  public function getActivationPlayer()
  {
    if ($this->getActivationCard() != null) {
      return $this->getActivationOCard()->getPlayer();
    } else {
      return null;
    }
  }

  /////////////////////////////////
  //  __  __  _____     _______
  // |  \/  |/ _ \ \   / / ____|
  // | |\/| | | | \ \ / /|  _|
  // | |  | | |_| |\ V / | |___
  // |_|  |_|\___/  \_/  |_____|
  /////////////////////////////////

  public function getPossibleMoves($maxMove = null, $maxMoveAttack = null, $additionalAction = true, $force = false)
  {
    if (!is_null($maxMove)) {
      $this->movementRadius = $maxMove;
    }

    if (!is_null($maxMoveAttack)) {
      $this->movementAndAttackRadius = $maxMoveAttack;
    }

    $pAction = [];
    if ($additionalAction) {
      foreach (Board::getTerrainsInCell($this->getPos()) as $terrain) {
        $actions = $terrain->getPossibleMoveActions($this);
        foreach ($actions as $action) {
          $action['type'] = 'action';
          $action['terrainId'] = $terrain->getId();
          $pAction[] = $action;
        }
      }

      // exit markers
      if (
        $this->getMoves() <
        $this->getMovementRadius() +
          (($this->getActivationOCard()->isType(CARD_BEHIND_LINES) ||
            $this->getActivationOCard()->isType(\CARD_INFANTRY_ASSAULT)) &&
          $this->getType() == \INFANTRY
            ? 1
            : 0)
      ) {
        $tokens = Tokens::getOnCoords('board', $this->getPos(), \TOKEN_EXIT_MARKER);
        $team = $this->getTeamId();
        foreach ($tokens as $t) {
          if ($t['team'] == $team) {
            $pAction[] = [
              'type' => 'action',
              'action' => 'actExitUnit',
              'desc' => \clienttranslate('Exit unit and gain medals'),
            ];
          }
        }
      }
    }

    return array_merge(Board::getReachableCells($this, $force), $pAction);
  }

  public function moveTo($cell)
  {
    $this->setX($cell['x']);
    $this->setY($cell['y']);
  }

  public function stayedOnRoad()
  {
    return $this->getExtraDatas('stayedOnRoad') ?? true;
  }

  public function leaveRoad()
  {
    return $this->setExtraDatas('stayedOnRoad', false);
  }

  public function getRoadBonus()
  {
    return $this->getExtraDatas('roadBonus') ?? 1;
  }

  public function hasUsedRoadBonus()
  {
    return ($this->getExtraDatas('roadBonus') ?? -1) == 0 ? true : false;
  }

  public function useRoadBonus()
  {
    return $this->setExtraDatas('roadBonus', 0);
  }

  public function mustStop()
  {
    return $this->setExtraDatas('stopped', true);
  }

  public function isStopped()
  {
    return $this->getExtraDatas('stopped') ?? false;
  }

  //////////////////////////////////////////
  //    _  _____ _____  _    ____ _  __
  //    / \|_   _|_   _|/ \  / ___| |/ /
  //   / _ \ | |   | | / _ \| |   | ' /
  //  / ___ \| |   | |/ ___ \ |___| . \
  // /_/   \_\_|   |_/_/   \_\____|_|\_\
  //////////////////////////////////////////

  // Could have used getMustSeeToAttack but more readable that way
  public function mustSeeToAttack()
  {
    return $this->mustSeeToAttack;
  }

  public function getTargetableUnits($overrideMoves = null)
  {
    return Board::getTargetableCells($this, null, $overrideMoves);
  }

  public function getHits($type, $nb)
  {
    $map = [\INFANTRY => \DICE_INFANTRY, \ARMOR => \DICE_ARMOR];
    if (isset($map[$this->type]) && $type == $map[$this->type]) {
      return $nb;
    }

    if ($type == \DICE_GRENADE) {
      return $nb;
    }

    return 0;
  }

  // used to get additional hits from special power of unit (sniper, etc.)
  public function getHitsOnTarget($type, $nb, $target)
  {
    return -1;
  }

  /*
   * return true if unit is dead
   */
  public function takeDamage($hits)
  {
    $hits = $hits >= $this->nUnits ? $this->nUnits : $hits;
    $this->incNUnits(-$hits);
    return $hits;
  }

  public function heal($heal)
  {
    $maxHeal = $this->maxUnits - $this->nUnits;
    $heal = $heal <= $maxHeal ? $heal : $maxHeal;
    $this->incNUnits($heal);
    return $heal;
  }

  public function isEliminated()
  {
    return $this->nUnits == 0;
  }

  public function cannotArmorOverrun()
  {
    return Board::cannotArmorOverrunCell(self::getPos());
  }

  public function afterAttack($coords, $hits, $eliminated)
  {
    return;
  }

  // Called if after an attack a retreat gave a hit
  public function afterAttackRetreatHit($coords, $hits, $eliminated)
  {
    return;
  }
}
