<?php
namespace M44\Core;
use M44\Core\Game;
use M44\Managers\Players;

/*
 * Globals
 */
class Globals extends \M44\Helpers\DB_Manager
{
  protected static $initialized = false;
  protected static $variables = [
    'changeActivePlayer' => 'obj', // Used for the generic "changeActivePlayer" state
    'mode' => 'int',
    'scenarioId' => 'int', // Used to store the scenario id
    'scenario' => 'obj', // Used to store the scenario

    'turn' => 'int',
    'teamTurn' => 'str', // Store which team is currently playing

    'unitMoved' => 'int', // Store last unit moved
    'unitAttacker' => 'int', // Store last unit that attacked

    'nToKeep' => 'int', // Number of cards to keep in the draw phase

    'attackStack' => 'obj', // Store all information of the ongoing attacks
    'retreat' => 'obj', // ['unit' => id, 'min' => min number of retreats hexes, 'max' => max number of retreat hexes]
  ];

  protected static $table = 'global_variables';
  protected static $primary = 'name';
  protected static function cast($row)
  {
    $val = json_decode(\stripslashes($row['value']), true);
    return self::$variables[$row['name']] == 'int' ? ((int) $val) : $val;
  }

  /*
   * Fetch all existings variables from DB
   */
  protected static $data = [];
  public static function fetch()
  {
    // Turn of LOG to avoid infinite loop (Globals::isLogging() calling itself for fetching)
    $tmp = self::$log;
    self::$log = false;

    foreach (
      self::DB()
        ->select(['value', 'name'])
        ->get(false)
      as $name => $variable
    ) {
      if (\array_key_exists($name, self::$variables)) {
        self::$data[$name] = $variable;
      }
    }

    self::$initialized = true;
    self::$log = $tmp;
  }

  /*
   * Create and store a global variable declared in this file but not present in DB yet
   *  (only happens when adding globals while a game is running)
   */
  public static function create($name)
  {
    if (!\array_key_exists($name, self::$variables)) {
      return;
    }

    $default = [
      'int' => 0,
      'obj' => [],
      'bool' => false,
      'str' => '',
    ];
    $val = $default[self::$variables[$name]];
    try {
      self::DB()->insert([
        'name' => $name,
        'value' => \json_encode($val),
      ]);
    } finally {
      self::$data[$name] = $val;
    }
  }

  /*
   * Magic method that intercept not defined static method and do the appropriate stuff
   */
  public static function __callStatic($method, $args)
  {
    if (!self::$initialized) {
      self::fetch();
    }

    if (preg_match('/^([gs]et|inc|is)([A-Z])(.*)$/', $method, $match)) {
      // Sanity check : does the name correspond to a declared variable ?
      $name = strtolower($match[2]) . $match[3];
      if (!\array_key_exists($name, self::$variables)) {
        throw new \InvalidArgumentException("Property {$name} doesn't exist");
      }

      // Create in DB if don't exist yet
      if (!\array_key_exists($name, self::$data)) {
        self::create($name);
      }

      if ($match[1] == 'get') {
        // Basic getters
        return self::$data[$name];
      } elseif ($match[1] == 'is') {
        // Boolean getter
        if (self::$variables[$name] != 'bool') {
          throw new \InvalidArgumentException("Property {$name} is not of type bool");
        }
        return (bool) self::$data[$name];
      } elseif ($match[1] == 'set') {
        // Setters in DB and update cache
        $value = $args[0];
        if (self::$variables[$name] == 'int') {
          $value = (int) $value;
        }
        if (self::$variables[$name] == 'bool') {
          $value = (bool) $value;
        }

        self::$data[$name] = $value;
        self::DB()->update(['value' => \addslashes(\json_encode($value))], $name);
        return $value;
      } elseif ($match[1] == 'inc') {
        if (self::$variables[$name] != 'int') {
          throw new \InvalidArgumentException("Trying to increase {$name} which is not an int");
        }

        $getter = 'get' . $match[2] . $match[3];
        $setter = 'set' . $match[2] . $match[3];
        return self::$setter(self::$getter() + (empty($args) ? 1 : $args[0]));
      }
    } else {
      throw new \feException('unknown method ' . $method);
      return null;
    }
    // return undefined;
  }

  /*
   * Setup new game
   */
  public static function setupNewGame($players, $options)
  {
    Globals::setMode($options[OPTION_MODE]);
    Globals::setScenarioId($options[OPTION_MODE + $options[OPTION_MODE]]);
    Globals::setUnitMoved(-1);
    Globals::setUnitAttacker(-1);
  }

  public static function isStandard()
  {
    return Globals::getMode() == OPTION_MODE_STANDARD;
  }

  public static function isBreakthrough()
  {
    return Globals::getMode() == OPTION_MODE_BREAKTHROUGH;
  }

  public static function isOverlord()
  {
    return Globals::getMode() == OPTION_MODE_OVERLORD;
  }
}
