<?php

namespace Kunlabo\Shared\Domain;

use DateTimeImmutable;
use DateTimeInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final class Utils
{

    public static function dateToString(DateTimeInterface $date): string
    {
        return $date->format(DateTimeInterface::ATOM);
    }

    public static function stringToDate(string $date): DateTimeImmutable
    {
        return new DateTimeImmutable($date);
    }

    public static function expandPath(string $original, string $path, array &$output): void
    {
        $arr = explode("/", $path, 2);
        if (count($arr) === 1) {
            $output[$arr[0]] = $original;
        } else {
            if (!array_key_exists($arr[0], $output)) {
                $output[$arr[0]] = [];
            }
            self::expandPath($original, $arr[1], $output[$arr[0]]);
        }
    }

    public static function fullyDeleteDir(string $dir): void
    {
        if (file_exists($dir)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $fileInfo) {
                $todo = ($fileInfo->isDir() ? 'rmdir' : 'unlink');
                $todo($fileInfo->getRealPath());
            }
            rmdir($dir);
        }
    }

    private const SILVER_RATIO = 0.618033988749895;

    public static function uniqueAlphaColor(int $n, float $alpha = 1.0): string
    {
        $h = 0.53;

        if ($n % 2 === 0) {
            $light = 50;
            $sat = 80;
        } else {
            $light = 35;
            $sat = 95;
        }

        for ($i = 0; $i < $n; $i++) {
            if ($i % 2 ===1) {
                $h += self::SILVER_RATIO;
            }

            if ($h > 1) {
                $h -= 1;
            }
        }

        $hue = $h * 360;

        return "hsla(" . round($hue) . ", $sat%, $light%, $alpha)";
    }

}
