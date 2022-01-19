<?php
namespace M44\Core;
use memoir;

/*
 * Game: a wrapper over table object to allow more generic modules
 */
class Game
{
  public static function get()
  {
    return memoir::get();
  }
}
