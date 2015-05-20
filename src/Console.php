<?php

namespace uzproger\migrator;

use yii\helpers\BaseConsole;

/**
 * Console helper class
 */
class Console extends BaseConsole
{
  /**
   * @inheritdoc
   */
    public static function select($prompt, $options = [])
    {
        top:
        $text = "";
        foreach ($options as $key => $value) {
            $text .= "     ".self::ansiFormat("[$key]", [self::FG_YELLOW])." - ".$value["name"]."\n";
        }
        $text .= "\n";
        static::stdout($text);
        echo "$prompt, (".self::ansiFormat("[q]", [self::FG_RED])." quit): ";
        $input = static::stdin();
        echo "\n";
        if ($input === 'q') {
            exit();
        } elseif (!array_key_exists($input, $options)) {
            echo "Please select right option.\n\n";
            goto top;
        } else {
            echo "You selected: ".$options[$input]["name"]."\n";
        }

        return $input;
    } 
}