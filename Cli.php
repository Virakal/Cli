<?php

namespace Virakal\Cli;

/**
 * Functions designed to make writing PHP command line scripts simpler.
 *
 * Currently these are all targeted towards UNIX-y systems.
 *
 * Interface likely to change drastically.
 *
 * @author Jonathan Goodger <jonno.is@gmail.com>
 * @license GPL3
 */
class Cli
{
    /** Escape pattern to begin a colour control code */
    const ESCAPE_PATTERN = "\033[%sm";
    /** Escape code to reset to original colours */
    const COLOUR_RESET = "\033[0m";

    /**
     * @staticvar array a list of codes for changing the foreground colour
     */
    public static $foregroundColours = array(
        'black'         => '0;30',
        'blue'          => '0;34',
        'brown'         => '0;33',
        'cyan'          => '0;36',
        'darkGray'      => '1;30',
        'darkGrey'      => '1;30',
        'green'         => '0;32',
        'lightBlue'     => '1;34',
        'lightCyan'     => '1;36',
        'lightGray'     => '0;37',
        'lightGreen'    => '1;32',
        'lightGrey'     => '0;37',
        'lightPurple'   => '1;35',
        'lightRed'      => '1;31',
        'purple'        => '0;35',
        'red'           => '0;31',
        'white'         => '1;37',
        'yellow'        => '1;33',
    );

    /**
     * @staticvar array a list of codes for changing the background colour
     */
    public static $backgroundColours = array(
        'black'         => '40',
        'blue'          => '44',
        'cyan'          => '46',
        'green'         => '42',
        'grey'          => '47',
        'lightGray'     => '47',
        'lightGrey'     => '47',
        'magenta'       => '45',
        'red'           => '41',
        'yellow'        => '43',
    );

    
    /**
     * @staticvar array a list of codes for changing the text formatting
     */
    public static $formatCodes = array(
        'blink'         => '5',
        'bold'          => '1',
        'dim'           => '2',
        'hidden'        => '8',
        'reverse'       => '7',
        'underline'     => '4',
    );

    /**
     * Format the given text.
     *
     * Notes:
     * - Some format options don't work together
     * - Output is largely dependent on the terminal and its settings
     * - Aliased as `color`
     *
     * @access public
     * @static
     * @author Jonathan Goodger <jonno.is@gmail.com>
     * @param  string $text the text to format
     * @param  string $foreground the foreground colour or null for none (default: null)
     * @param  string $background the background colour or null for none (default: null)
     * @param  string|array $format an additional formatting options, or an array of them
     * @return string 
     */
    public static function colour($text, $foreground=null, $background=null, $format=array())
    {
        return self::getForegroundCode($foreground)
            . self::getBackgroundCode($background)
            . implode(array_map(function ($x) {return self::getFormatCode($x);}, $format))
            . $text . self::getColourReset();
    }

    /**
     * Format the given text and echo it.
     *
     * @see colour for argument docs
     * @access public
     * @static
     * @author Jonathan Goodger <jonno.is@gmail.com>
     */
    public static function say($text, $foreground=null, $background=null, $format=array())
    {
        $ret = self::colour($text, $foreground, $background, $format) . PHP_EOL;

        echo $ret;

        return $ret;
    }

    /**
     * @see colour an alias
     * @access public
     * @static
     * @author Jonathan Goodger <jonno.is@gmail.com>
     */
    public static function color($text, $foreground=null, $background=null, $format=array())
    {
        return self::colour($text, $foreground, $background, $format);
    }

    public static function getForegroundCode($colour=null)
    {
        if (isset(self::$foregroundColours[$colour])) {
            return sprintf(self::ESCAPE_PATTERN, self::$foregroundColours[$colour]);
        }

        return '';
    }

    public static function getBackgroundCode($colour=null)
    {
        if (isset(self::$backgroundColours[$colour])) {
            return sprintf(self::ESCAPE_PATTERN, self::$backgroundColours[$colour]);
        }

        return '';
    }

    public static function getFormatCode($format=null)
    {
        if (isset(self::$formatCodes[$format])) {
            return sprintf(self::ESCAPE_PATTERN, self::$formatCodes[$format]);
        }

        return '';
    }

    public static function getColourReset()
    {
        return self::COLOUR_RESET;
    }

    /**
     * Sound the system bell.
     * 
     * @access public
     * @static
     * @author Jonathan Goodger <jonno.is@gmail.com>
     * @param  integer $repeat number of times to repeat the bell (default: 1)
     * @return void
     */
    public static function bell($repeat=1) {
        echo str_repeat("\007", $repeat);
    }

    /**
     * Execute a shell command, displaying the output and allowing the user to interact.
     *
     * @access public
     * @static
     * @author Jonathan Goodger <jonno.is@gmail.com>
     * @param  string $cmd the command to run
     * @param  string $cwd the working directory for the command (default: the current working directory)
     * @param  array  $env an array of environment variables (default: the current system environment)
     * @return integer 
     * @see    proc_open for more details on arguments
     */
    public static function execInteractive($cmd, $cwd=null, $env=null)
    {
        $proc = proc_open($cmd, array(STDIN, STDOUT, STDERR), $pipes, $cwd, $env);

        return proc_close($proc);
    }
}