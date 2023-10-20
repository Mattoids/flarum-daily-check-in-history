<?php

namespace Mattoid\CheckinHistory\Model;

use Flarum\Database\AbstractModel;
use Flarum\Formatter\Formatter;

class UserCheckinHistory extends AbstractModel
{
    protected $table = "user_checkin_history";

    /**
     * The text formatter instance.
     *
     * @var \Flarum\Formatter\Formatter
     */
    protected static $formatter;

    /**
     * Get the text formatter instance.
     *
     * @return \Flarum\Formatter\Formatter
     */
    public static function getFormatter()
    {
        return static::$formatter;
    }

    /**
     * Set the text formatter instance.
     *
     * @param \Flarum\Formatter\Formatter $formatter
     */
    public static function setFormatter(Formatter $formatter)
    {
        static::$formatter = $formatter;
    }
}
