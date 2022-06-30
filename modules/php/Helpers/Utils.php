<?php
namespace M44\Helpers;
use M44\Scenario;

abstract class Utils extends \APP_DbObject
{
  public static function filter(&$data, $filter)
  {
    $data = array_values(array_filter($data, $filter));
  }

  public static function die($args = null)
  {
    if (is_null($args)) {
      throw new \BgaVisibleSystemException(implode('<br>', self::$logmsg));
    }
    throw new \BgaVisibleSystemException(json_encode($args));
  }

  public static function diff(&$data, $arr)
  {
    $data = array_values(array_diff($data, $arr));
  }

  public static function shuffle_assoc(&$array)
  {
    $keys = array_keys($array);
    shuffle($keys);

    foreach ($keys as $key) {
      $new[$key] = $array[$key];
    }

    $array = $new;
    return true;
  }

  public static function array_usearch($array, $test)
  {
    $found = false;
    $iterator = new \ArrayIterator($array);

    while ($found === false && $iterator->valid()) {
      if ($test($iterator->current())) {
        $found = $iterator->key();
      }
      $iterator->next();
    }

    return $found;
  }

  public static function searchCell($cells, $x, $y = null)
  {
    if ($y === null) {
      $y = $x['y'];
      $x = $x['x'];
    }

    return self::array_usearch($cells, function ($cell) use ($x, $y) {
      return $cell['x'] == $x && $cell['y'] == $y;
    });
  }

  public static function filterCells(&$cells, $fCells)
  {
    self::filter($cells, function ($cell) use ($fCells) {
      foreach ($fCells as $node) {
        if ($node['x'] == $cell['x'] && $node['y'] == $cell['y']) {
          return true;
        }
      }
      return false;
    });
  }

  public static function privatise($data, $pId = null)
  {
    $key = $pId ?? 'active';
    return [
      '_private' => [
        $key => $data,
      ],
    ];
  }

  /**
   * This function is just used to remove some informations UI-useless information before sending to front in args
   */
  public static function clearPaths(&$units, $clearPaths = true)
  {
    if (!$clearPaths || empty($units)) {
      return;
    }

    foreach ($units as &$cells) {
      foreach ($cells as &$cell) {
        unset($cell['paths']);
      }
    }
  }

  public static function computeCoords($x, $y = null)
  {
    if (!is_array($x)) {
      $x = ['x' => $x, 'y' => $y];
    }

    $height = Scenario::getMode() == BREAKTHROUGH_DECK ? 17 : 9;
    // capital
    if ($x['x'] % 2 == 0) {
      return strtoupper(alphabet[$x['x'] / 2]) . ($height - $x['y']);
    } else {
      return alphabet[($x['x'] - 1) / 2] . ($height - $x['y']);
    }
  }

  public static function revertCoords($coord)
  {
    $xy = ['x' => substr($coord, 0, 1), 'y' => substr($coord, 1)];

    $v = \array_search($xy['x'], alphabet);
    if ($v === false) {
      $v = \array_search(strtolower($xy['x']), alphabet);
      $xy['x'] = $v * 2;
    } else {
      $xy['x'] = $v * 2 + 1;
    }


    $height = Scenario::getMode() == BREAKTHROUGH_DECK ? 17 : 9;
    $xy['y'] = $height - $xy['y'];
    return $xy;

    // capital
    if ($x['x'] % 2 == 0) {
      return strtoupper(alphabet[$x['x'] / 2]) . ($height - $x['y']);
    } else {
      return alphabet[($x['x'] - 1) / 2] . ($height - $x['y']);
    }
  }
}
