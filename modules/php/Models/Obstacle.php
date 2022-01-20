<?php
namespace M44\Models;

class Obstacle implements \JsonSerializable
{
  /*
   * STATIC INFORMATIONS
   */
  protected $type = null;
  protected $name = '';
  protected $manmade = '';

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
}
