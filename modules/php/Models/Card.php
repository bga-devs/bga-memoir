<?php
namespace M44\Models;
use M44\Managers\Players;
use M44\Managers\Units;
use M44\Managers\Cards;
use M44\Core\Game;

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
  protected $cannotIgnoreFlags = false;
  protected $hitMap = [
    \DICE_FLAG => false,
    \DICE_STAR => false,
    \DICE_ARMOR => false,
    \DICE_GRENADE => false,
    \DICE_INFANTRY => false,
  ];

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
    return $this->counterAttackCardId ?? $this->id;
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

  public function cannotIgnoreFlags()
  {
    return $this->cannotIgnoreFlags;
  }

  public function isType($cardType)
  {
    return $this->getType() == $cardType;
  }

  public function getNotifString()
  {
    return null;
  }

  public function onPlay()
  {
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

  public function getExtraDatas($variable, $recurseIfCopied = true)
  {
    if ($recurseIfCopied && !is_null($this->counterAttackCardId)) {
      return Cards::get($this->counterAttackCardId)->getExtraDatas($variable);
    }

    return $this->extraDatas[$variable] ?? null;
  }

  public function setExtraDatas($variable, $value)
  {
    if (!is_null($this->counterAttackCardId)) {
      return Cards::get($this->counterAttackCardId)->setExtraDatas($variable, $value);
    }

    $this->extraDatas[$variable] = $value;
    self::DB()->update(['extra_datas' => \addslashes(\json_encode($this->extraDatas))], $this->id);
  }

  public function clearExtraDatas()
  {
    $this->extraDatas = [];
    self::DB()->update(['extra_datas' => \addslashes(\json_encode([]))], $this->id);
  }

  public function getDrawMethod()
  {
    return $this->draw;
  }

  public function getDiceModifier($unit, $cell)
  {
    return 0;
  }

  public function getHits($type, $nb)
  {
    if ($this->hitMap[$type]) {
      return $nb;
    }

    return -1;
  }

  public function getActivatedUnits()
  {
    return Units::getActivatedByCard($this);
  }

  public function getActivatedUnit()
  {
    return $this->getActivatedUnits()->first();
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
    $units = $this->getActivatedUnits();

    return [
      'units' => $units->map(function ($unit) {
        return $unit->getPossibleMoves();
      }),
    ];
  }

  public function getAdditionalPlayConstraints()
  {
    return null;
  }

  public function canHill317()
  {
    return false;
  }

  /**
   *
   * @param $overrideNbFights = [UNIT_TYPE => maxFights]]
   *
   **/
  public function getArgsAttackUnits($overrideNbFights = null)
  {
    $player = $this->getPlayer();
    $units = $this->getActivatedUnits()->filter(function ($unit) {
      return !$unit->isOnTheMove();
    });

    return [
      'units' => $units->map(function ($unit) use ($overrideNbFights) {
        $maxFights = $overrideNbFights[$unit->getType()] ?? $this->nbFights;
        if ($unit->cannotBattle() || ($unit->getFights() >= $maxFights && !$unit->canBattleAndRemoveWire())) {
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
    if (!$unit->canOverrun() || $unit->getFights() > 1 || $unit->cannotArmorOverrun()) {
      // TODO : this would break if a card allow an armor to fight twice
      return ['unit' => []];
    }

    return [
      'units' => [
        $unit->getId() => $unit->getTargetableUnits(),
      ],
      'lastUnitAttacker' => $unit->getId(),
    ];
  }

  ////////////////////////////////////////////////////////////////////////
  //   ____                  _             _   _   _             _
  //  / ___|___  _   _ _ __ | |_ ___ _ __ / \ | |_| |_ __ _  ___| | __
  // | |   / _ \| | | | '_ \| __/ _ \ '__/ _ \| __| __/ _` |/ __| |/ /
  // | |__| (_) | |_| | | | | ||  __/ | / ___ \ |_| || (_| | (__|   <
  //  \____\___/ \__,_|_| |_|\__\___|_|/_/   \_\__|\__\__,_|\___|_|\_\
  ////////////////////////////////////////////////////////////////////////

  protected $isCounterAttackMirror = false;
  protected $counterAttackCardId = null;
  public function setCounterAttack($pId, $cardId, $isCounterAttackMirror)
  {
    $this->pId = $pId;
    $this->isCounterAttackMirror = !$isCounterAttackMirror;
    $this->counterAttackCardId = $cardId;
  }

  public function mirrorSection($sectionId)
  {
    $map = [0 => 2, 1 => 1, 2 => 0];
    // TODO: overlord?
    return $map[$sectionId] ?? null;
  }
}
