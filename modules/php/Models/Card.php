<?php
namespace M44\Models;
use M44\Managers\Players;

class Card implements \JsonSerializable
{
  /*
   * STATIC INFORMATIONS
   */
  protected $type = null;
  protected $name = '';
  protected $subtitle = '';
  protected $text = '';
  protected $deck = STANDARD_DECK;


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
      $this->state = $row['state'];
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

  public function getState()
  {
    return $this->state;
  }

  public function jsonSerialize()
  {
    return [
      'id' => $this->id,
      'pId' => $this->pId,
      'name' => $this->name,
      'location' => $this->location,
      'state' => $this->state,
      'tooltip' => $this->tooltip,
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
}
