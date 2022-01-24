<?php
namespace M44\Models;

class RectTerrain extends Terrain
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
}
