<?php
namespace M44\Models;

class Terrain implements \JsonSerializable
{
  /*
   * STATIC INFORMATIONS
   */
  protected $type = null;
  protected $name = '';
  protected $landscape = '';
  protected $vegetation = false;
  protected $elevation = false;
  protected $manmade = false;
  protected $buildings = false;
  protected $landmark = false;
  protected $water = false;
  protected $road = false;
  protected $air = false;
  protected $rail = false;

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
