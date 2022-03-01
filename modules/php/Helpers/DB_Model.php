<?php
namespace M44\Helpers;
use M44\Core\Game;

abstract class DB_Model extends \APP_DbObject implements \JsonSerializable
{
  protected $table = null;
  protected $primary = null;
  protected $log = null;
  /**
   * This associative array will link class attributes to db fields
   */
  protected $attributes = [];

  /**
   * This array will contains class attributes that does not depends on the DB (static info), they can only be accessed, not modified
   */
  protected $staticAttributes = [];

  /**
   * Fill in class attributes based on DB entry
   */
  public function __construct($row)
  {
    foreach ($this->attributes as $attribute => $field) {
      $fieldName = is_array($field) ? $field[0] : $field;
      $this->$attribute = $row[$fieldName] ?? null;
      if (is_array($field)) {
        if ($field[1] == 'int') {
          $this->$attribute = (int) $this->$attribute;
        }
        if ($field[1] == 'bool') {
          $this->$attribute = (bool) $this->$attribute;
        }
        if ($field[1] == 'obj') {
          $this->$attribute = json_decode($this->$attribute, true);
        }
      }
    }
  }

  /**
   * Get the DB primary row according to attributes mapping
   */
  private function getPrimaryFieldValue()
  {
    foreach ($this->attributes as $attribute => $field) {
      $fieldName = is_array($field) ? $field[0] : $field;
      if ($fieldName == $this->primary) {
        return $this->$attribute;
      }
    }
    return null;
  }

  /*
   * Magic method that intercept not defined method and do the appropriate stuff
   */
  public function __call($method, $args)
  {
    if (preg_match('/^([gs]et|inc|is)([A-Z])(.*)$/', $method, $match)) {
      // Sanity check : does the name correspond to a declared variable ?
      $name = strtolower($match[2]) . $match[3];
      if (!\array_key_exists($name, $this->attributes)) {
        // Static attribute getters
        if (in_array($name, $this->staticAttributes) && $match[1] == 'get') {
          return $this->$name;
        } else {
          throw new \InvalidArgumentException("Attribute {$name} doesn't exist");
        }
      }

      if ($match[1] == 'get') {
        if (count($args) > 0 && is_array($this->attributes[$name]) && $this->attributes[$names][1] == 'obj') {
          // Handle json field
          return $this->$name[$args[0]];
        } else {
          // Basic getters
          return $this->$name;
        }
      } elseif ($match[1] == 'is') {
        // Boolean getter
        return (bool) ($this->$name == 1);
      } elseif ($match[1] == 'set') {
        // Setters in DB and update cache
        $value = $args[0];

        // Auto-cast
        $field = $this->attributes[$name];
        $fieldName = is_array($field) ? $field[0] : $field;
        $isObj = false;
        if (is_array($field)) {
          if ($field[1] == 'int') {
            $value = (int) $value;
          }
          if ($field[1] == 'bool') {
            $value = (bool) $value;
          }
          if ($field[1] == 'obj') {
            $isObj = true;
            $value = count($args) > 1 ? $args[1] : $args[0];
            $objKey = count($args) > 1 ? $args[0] : null;
          }
        }

        if ($isObj && $objKey !== null) {
          $this->$name[$objKey] = $value;
        } else {
          $this->$name = $value;
        }

        $updateValue = $this->$name;
        if ($isObj) {
          $updateValue = json_encode($updateValue);
        }
        if ($value != null) {
          $updateValue = \addslashes($value);
        }

        // $this->DB()->update([$this->attributes[$name] => \addslashes($value)], $this->getPrimaryFieldValue());
        $this->DB()->update([$fieldName => $updateValue], $this->getPrimaryFieldValue());
        return $value;
      } elseif ($match[1] == 'inc') {
        $getter = 'get' . $match[2] . $match[3];
        $setter = 'set' . $match[2] . $match[3];
        return $this->$setter($this->$getter() + (empty($args) ? 1 : $args[0]));
      }
    } else {
      throw new \feException('Undefined method ' . $method);
      return null;
    }
  }

  /**
   * Return an array of attributes
   */
  public function jsonSerialize()
  {
    $data = [];
    foreach ($this->attributes as $attribute => $field) {
      $data[$attribute] = $this->$attribute;
    }

    return $data;
  }

  /**
   * Private DB call
   */
  private function DB()
  {
    if (is_null($this->table)) {
      throw new \feException('You must specify the table you want to do the query on');
    }

    $log = null;
    /*
    if (static::$log ?? Game::get()->getGameStateValue('logging') == 1) {
      $log = new Log(static::$table, static::$primary);
    }
    */
    return new QueryBuilder(
      $this->table,
      function ($row) {
        return $row;
      },
      $this->primary,
      $log
    );
  }
}
