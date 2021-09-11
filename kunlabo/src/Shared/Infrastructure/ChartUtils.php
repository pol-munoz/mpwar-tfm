<?php

namespace Kunlabo\Shared\Infrastructure;

final class ChartUtils
{
    const POINT_STYLES = ['circle', 'rect', 'rectRounded', 'rectRot', 'triangle'];
    const POINT_STYLES_NUMBER = 5;

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

    public static function titleConfig(string $text): array
    {
        return [
            'display' => true,
            'text' => $text,
            'fontFamily' => "'Poppins', sans-serif",
            'fontSize' => 18,
            'fontColor' => '#040910',
            'padding' => 20
        ];
    }

    public static function axisLabelConfig(string $text): array
    {
        return [
            'display' => true,
            'labelString' => $text,
            'fontSize' => 14,
            'fontColor' => '#1d3d70',
            'fontStyle' => 'bold',
            'padding' => 10
        ];
    }
}