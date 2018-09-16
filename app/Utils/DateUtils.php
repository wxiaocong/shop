<?php namespace App\Utils;

/**
 * DateUtils
 * 2016-05-19
 * @author Jakty Ling(lingjun@carnetmotor.com)
 *
 */
class DateUtils
{
    const Y      = 'Y';
    const YM     = 'Y-m';
    const YMD    = 'Y-m-d';
    const M      = 'm';
    const MD     = 'm-d';
    const YMDHMS = 'Y-m-d H:i:s';
    const YMDHM  = 'Y-m-d H:i';

    /**
     * format
     *
     * @param string $date yyyy-mm-dd defaut now
     * @param string $format default 'Y-m-d'
     *
     * @return string yyyy-mm-dd
     */
    public static function format($date = '', $format = self::YMD)
    {
        return date($format, strtotime($date));
    }

    /**
     * addDay
     *
     * @param int $addDay add day
     * @param string $date yyyy-mm-dd defaut now
     * @param string $format
     *
     * @return string yyyy-mm-dd
     */
    public static function addDay($addDay = 0, $date = '', $format = self::YMD)
    {
        if (static::isDate($date)) {
            return date($format, strtotime($date . $addDay . ' days'));
        }
        return date($format, strtotime($addDay . ' days'));
    }

    /**
     * minusDay
     *
     * @param int $minusDay minus day
     * @param string $date yyyy-mm-dd defaut now
     *
     * @return string yyyy-mm-dd
     */
    public static function minusDay($minusDay = 0, $date = '')
    {
        if (static::isDate($date)) {
            return date(self::YMD, strtotime($date . '-' . $minusDay . ' days')) . ' 23:59:59';
        }
        return date(self::YMD, strtotime($minusDay . ' days')) . ' 23:59:59';
    }

    /**
     * addMonth
     *
     * @param int $addMonth add month
     * @param string $date yyyy-mm-dd  defaut now
     * @param string $format
     *
     * @return string yyyy-mm-dd
     */
    public static function addMonth($addMonth = 0, $date = '', $format = self::YMD)
    {
        if (static::isDate($date)) {
            return date($format, strtotime($date . $addMonth . ' months'));
        }
        return date($format, strtotime($addMonth . ' months'));
    }

    /**
     * addYear
     *
     * @param int $addYear add year
     * @param string $date yyyy-mm-dd defaut now
     * @param string $format
     *
     * @return string yyyy-mm-dd
     */
    public static function addYear($addYear = 0, $date = '', $format = self::YMD)
    {
        if (static::isDate($date)) {
            return date($format, strtotime($date . $addYear . ' years'));
        }
        return date($format, strtotime($addYear . ' years'));
    }

    /**
     * isDate
     *
     * @param string $date yyyy-mm-dd
     *
     * @return boolean
     */
    public static function isDate($date = '')
    {
        $dateArr = explode('-', $date);
        if (count($dateArr) === 3 && is_numeric($dateArr[0]) && is_numeric($dateArr[1]) && is_numeric($dateArr[2])) {
            if (checkdate($dateArr[1], $dateArr[2], $dateArr[0])) {
                return true;
            }
        }
        return false;
    }

    /**
     * dateDiff
     *
     * @param string $endDate yyyy-mm-dd defaut ''
     * @param string $startDate yyyy-mm-dd defaut '
     *
     * @return int
     */
    public static function dateDiff($endDate = '', $startDate = '')
    {
        if (!static::isDate($endDate)) {
            $endDate = date(self::YMD, time() + 3600 * 24);
        }
        if (!static::isDate($startDate)) {
            $startDate = date(self::YMD, time());
        }
        return round((strtotime($endDate) - strtotime($startDate)) / 24 / 3600);
    }

    /**
     * newDate
     *
     * @param string $dateFormat yyyy-mm-dd
     *
     * @return string
     */
    public static function newDate($dateFormat = self::YMD)
    {
        return date($dateFormat, time());
    }
}
