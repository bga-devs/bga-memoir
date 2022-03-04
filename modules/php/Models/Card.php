<?php
namespace M44\Models;
use M44\Managers\Players;
use M44\Managers\Units;

class Card extends \M44\Helpers\DB_Manager implements \JsonSerializable
{
  protected static $table = 'cards';
  protected static $primary = 'card_id';

  /*
   * STATIC INFORMATIONS
   */
  protected $type = null;
  protected $name = '';
  protected $subtitle = '';
  protected $text = '';
  protected $deck = STANDARD_DECK;
  protected $draw = ['nDraw' => 1, 'nKeep' => 1]; // Number of card to draw / to keep
  protected $nbFights = 1;

  /*
   * DYNAMIC INFORMATIONS
   */
  protected $id = -1;
  protected $value = 0;
  protected $location = '';
  protected $pId = null;
  protected $state = 0;

  public function __construct($row)
  {
    if ($row != null) {
      $this->id = $row['id'];
      $this->location = $row['location'];
      $this->pId = $row['player_id'];
      $this->state = (int) $row['state'];
      $this->value = (int) $row['value'];
      $this->extraDatas = json_decode(\stripslashes($row['extra_datas']), true);
      if ($this->pId != null) {
        $this->pId = (int) $this->pId;
      }
    }
  }

  /*
   * Getters
   */
  public function getId()
  {
    return $this->id;
  }
  public function getPId()
  {
    return $this->pId;
  }
  public function getPlayer()
  {
    $pId = $this->getPId();
    return $pId == null ? $pId : Players::get($pId);
  }
  public function getName()
  {
    return $this->name;
  }

  public function getLocation()
  {
    return $this->location;
  }

  public function getType()
  {
    return $this->type;
  }

  public function getState()
  {
    return $this->state;
  }
  public function getText()
  {
    return $this->text;
  }
  public function getSubtitle()
  {
    return $this->subtitle;
  }

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'pId' => $this->pId,
      'location' => $this->location,
      'state' => $this->state,
      'type' => $this->type,
      'value' => $this->value,
      'name' => $this->name,
      'subtitle' => $this->getSubtitle(),
      'text' => $this->getText(),
    ];
  }

  public function getExtraDatas($variable)
  {
    return $this->extraDatas[$variable] ?? null;
  }

  public function setExtraDatas($variable, $value)
  {
    $this->extraDatas[$variable] = $value;
    self::DB()->update(['extra_datas' => \addslashes(\json_encode($this->extraDatas))], $this->id);
  }

  public function getDrawMethod()
  {
    return $this->draw;
  }

  public function getDiceModifier($unit, $cell)
  {
    return 0;
  }

  //////////////////////////////////////////////////////
  //  _____ _                    _
  // |  ___| | _____      __    / \   _ __ __ _ ___
  // | |_  | |/ _ \ \ /\ / /   / _ \ | '__/ _` / __|
  // |  _| | | (_) \ V  V /   / ___ \| | | (_| \__ \
  // |_|   |_|\___/ \_/\_/   /_/   \_\_|  \__, |___/
  //                                      |___/
  //////////////////////////////////////////////////////
  public function nextStateAfterPlay()
  {
    return 'selectUnits';
  }

  public function getArgsOrderUnits()
  {
    return [
      'n' => '',
      'desc' => '',
    ];
  }

  public function nextStateAfterOrder($unitIds, $onTheMoveIds)
  {
    return 'moveUnits';
  }

  public function getArgsMoveUnits()
  {
    $player = $this->getPlayer();
    $units = Units::getActivatedByCard($this);

    return [
      'units' => $units->map(function ($unit) {
        return $unit->getPossibleMoves();
      }),
    ];
  }

  public function getArgsAttackUnits()
  {
    $player = $this->getPlayer();
    $units = Units::getActivatedByCard($this);

    /*
    // check if there is a unit already fighting
    $forceUnit = $units->filter(function ($unit) {
      return $unit->getFights() != 0 && $unit->getFights() < $this->nbFights;
    });

    if (count($forceUnit) != 0) {
      $id = $forceUnit->getIds()[0];
      return ['units' => [$id => $units[$id]->getTargetableUnits()]];
    }
*/

    return [
      'units' => $units->map(function ($unit) {
        if ($unit->getFights() >= $this->nbFights) {
          return [];
        }
        return $unit->getTargetableUnits();
      }),
    ];
  }

  public function nextStateAfterAttacks()
  {
    return 'draw';
  }


  public function getArgsArmorOverrun($unitId)
  {
    $unit = Units::get($unitId);
    if ($unit->getType() != ARMOR || $unit->getFights() > 1) {
      // TODO : this would break if a card allow an armor to fight twice
      return ['unit' => []];
    }

    return [
      'units' => [
        $unit->getId() => $unit->getTargetableUnits(),
      ],
    ];
  }
}
