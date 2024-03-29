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
}
