<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Geometry for a server-rendered line chart. Coordinates are in the units of
 * the chart's SVG view box.
 */
final readonly class LineChart
{
    public const int WIDTH = 640;

    public const int HEIGHT = 260;

    public const int TOP = 12;

    public const int BOTTOM = 228;

    public const int LEFT = 44;

    public const int RIGHT = 628;

    private const int TICK_COUNT = 4;

    private const int MAX_TICK_LABELS_WHEN_NARROW = 7;

    public int $maximum;

    /** @var list<int> */
    public array $ticks;

    /**
     * @param  list<string>  $labels
     * @param  array<string, list<int>>  $series  Values for each label, keyed by series name.
     */
    public function __construct(
        public array $labels,
        public array $series,
    ) {
        $highest = max([1, ...array_merge(...array_values($series))]);
        $step = self::niceStep($highest / self::TICK_COUNT);

        $this->maximum = (int) (ceil($highest / $step) * $step);
        $this->ticks = range(0, $this->maximum, $step);
    }

    public function isEmpty(): bool
    {
        return array_sum(array_merge(...array_values($this->series))) === 0;
    }

    public function x(int $index): float
    {
        $count = count($this->labels);

        if ($count === 1) {
            return (self::LEFT + self::RIGHT) / 2;
        }

        return round(self::LEFT + (self::RIGHT - self::LEFT) * $index / ($count - 1), 2);
    }

    public function y(int $value): float
    {
        return round(self::BOTTOM - (self::BOTTOM - self::TOP) * $value / $this->maximum, 2);
    }

    /**
     * @param  list<int>  $values
     */
    public function path(array $values): string
    {
        $points = array_map(
            fn (int $value, int $index): string => ($index === 0 ? 'M' : 'L').$this->x($index).' '.$this->y($value),
            $values,
            array_keys($values),
        );

        return implode(' ', $points);
    }

    /**
     * Crowded axes show every third label on narrow screens, ending at the last.
     */
    public function isMinorLabel(int $index): bool
    {
        $count = count($this->labels);

        return $count > self::MAX_TICK_LABELS_WHEN_NARROW && ($count - 1 - $index) % 3 !== 0;
    }

    /**
     * The width of the hover column centered on a point.
     */
    public function columnWidth(): float
    {
        return (self::RIGHT - self::LEFT) / max(1, count($this->labels) - 1);
    }

    private static function niceStep(float $rough): int
    {
        foreach ([1, 2, 5, 10, 20, 25, 50, 100, 200, 250, 500, 1000, 2000, 5000, 10000] as $step) {
            if ($step >= $rough) {
                return $step;
            }
        }

        return (int) (ceil($rough / 10000) * 10000);
    }
}
