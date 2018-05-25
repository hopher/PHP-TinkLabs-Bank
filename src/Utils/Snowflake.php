<?php

namespace TinkLabs\Bank\Utils;

/**
 * SnowFlake算法，用于生成分布式自增唯一ID
 *
 * 每秒产生约400W个不同的16位数字ID(10进制)
 */

class Snowflake
{
    //开始时间 - 固定一个小于当前时间的毫秒数即可
    const EPOCH = 1479533469598;
    const max12bit = 4095;
    const max41bit = 1099511627775;

    static $machineId = null;

    public static function machineId($mId = 0)
    {
        self::$machineId = $mId;
    }

    public static function generateId()
    {
        /*
         * Time - 42 bits
         */
        $time = floor(microtime(true) * 1000);

        /*
         * Substract custom epoch from current time
         */
        $time -= self::EPOCH;

        /*
         * Create a base and add time to it
         */
        $base = decbin(self::max41bit + $time);

        /*
         * Configured machine id - 10 bits - up to 1024 machines
         */
        if (!self::$machineId) {
            $machineid = self::$machineId;
        } else {
            $machineid = str_pad(decbin(self::$machineId), 10, "0", STR_PAD_LEFT);
        }

        /*
         * sequence number - 12 bits - up to 4096 random numbers per machine
         */
        $random = str_pad(decbin(mt_rand(0, self::max12bit)), 12, "0", STR_PAD_LEFT);

        /*
         * Pack
         */
        $base = $base . $machineid . $random;

        /*
         * Return unique time id no
         */
        return bindec($base);
    }

    public static function getTimeFromId($id)
    {
        /*
         * Return time
         */
        return bindec(substr(decbin($id), 0, 41)) - self::max41bit + self::EPOCH;
    }

    /**
     * 产生随机数
     */
    public static function randomString($len = 6)
    {
        $chars = [
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
            "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
            "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
            "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
            "3", "4", "5", "6", "7", "8", "9",
        ];
        $charsLen = count($chars) - 1;
        shuffle($chars); // 将数组打乱
        $output = "";
        for ($i = 0; $i < $len; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }
        return $output;
    }
}
