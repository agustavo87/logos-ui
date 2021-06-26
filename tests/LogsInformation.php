<?php

namespace Tests;

use function Arete\Common\var_dump_ret;

trait LogsInformation
{
    /**
     * Si presenta emisiones de Debug.
     *
     * @var bool
     */
    public static bool $debug = true;

     /**
     * Si presenta emisiones de log() u ok().
     *
     * @var bool
     */
    public static bool $verbose = true;

    /**
     * Emite información de debug.
     *
     * @return void
     */
    public static function debug(string $message): void
    {
        if (static::$debug) {
            $message = self::toLoggable($message);
            fwrite(STDOUT, "\033[0;93;43m > \033[0m {$message}\n");
        }
    }

     /**
     * Emite información de advertencia.
     *
     * Ee emite siemptre
     * @return void
     */
    public static function warn($message)
    {
        $message = self::toLoggable($message);
        fwrite(STDOUT, "\033[0;30;43m > \033[0;93m {$message} \033[0m\n");
    }

     /**
     * Emite información a la terminal.
     *
     * @param bool $ok indica que algo salió bien.
     * @return void
     */
    public static function log($message, $ok = false): void
    {
        if (! is_string($message)) {
            $message = var_dump_ret($message);
        }
        $color = $ok ? "32" : "37";
        if (static::$verbose) {
            $message = self::toLoggable($message);
            fwrite(STDOUT, "\033[1;{$color}m > \033[0m {$message}\n");
        }
    }

     /**
     * Convierte valores (objetos) a cadenas.
     *
     * @return string
     */
    public static function toLoggable($input): string
    {
        if (!is_string($input)) {
            return print_r($input, true);
        }
        return $input;
    }

     /**
     * Emite información de algo que ocurrió correctamente
     *
     * @return string
     */
    public static function ok($message, $ok = false): void
    {
        self::log($message, true);
    }
}
