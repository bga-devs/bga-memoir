<?php
namespace M44\Models;
use M44\Board;
use M44\Core\Notifications;

class Obstacle extends Terrain
{
  /*
   * STATIC INFORMATIONS
   */
  protected $type = null;
  protected $name = '';
  protected $manmade = '';

  public function removeFromBoard()
  {
    Board::removeTerrain($this);
    Notifications::removeObstacle($this);
  }
}
