<?php

class DateHelper
{
    public static function stringToDate($date_string)
    {
        //d --- 01 to 31 or 1 to 31
        //m  --- 01 through 12 or 1 through 12
        //Y --- Examples: 1999 or 2003

        //H --- 0 through 23 or 00 through 23
        //i	Minutes with leading zeros	00 to 59
        //s	Seconds, with leading zeros	00 through 59
        //u	Microseconds (up to six digits)	Example: 45, 654321

        //U	Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)	Example: 1292177455

        //! --- always use to drop default values
        $utcTimeZone = new DateTimeZone('UTC');
        $date = DateTime::createFromFormat('!Y-m-d H:i:s', $date_string, $utcTimeZone);
        return $date;
    }

    public static function dateToString($date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public static function dateUnixToString($dateUnix)
    {
        return date('Y-m-d H:i:s', $dateUnix);
    }

    public static function stringToDateUnix($date_string)
    {
        return strtotime($date_string);
    }

    public static function dateToDateUnix($date)
    {
        return (int)$date->format('U');
    }

    public static function currentDate()
    {
        $utcTimeZone = new DateTimeZone('UTC');
        $date = new DateTime(null, $utcTimeZone);
        return $date;
    }

    public static function dayTimeToSeconds($hours, $minutes, $seconds, $inc = 0)
    {
        return intval($hours) * 60 * 60 + intval($minutes) * 60 + intval($seconds) + $inc;
    }

}
