<?php
namespace M44\Models;

class RectTerrain implements \JsonSerializable
{
  /*
   * STATIC INFORMATIONS
   */
  protected $type = null;
  protected $name = '';
  protected $landscape = '';
  protected $bunker = false;
  protected $transport = false;
  protected $bridge = false;
  protected $water = false;
  protected $road = false;
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
