@props(['chart', 'ranges', 'caption'])

@php
    use App\Support\LineChart;

    $seriesNames = array_keys($chart->series);
@endphp

<div {{ $attributes->class('chart') }} x-data="chart" x-on:pointerover="show" x-on:focusin="show" x-on:pointerleave="hide" x-on:focusout="hide">
    <ul class="chart__legend" role="list">
        @foreach ($seriesNames as $index => $name)
            <li><span class="chart__key chart__key--{{ $index + 1 }}"></span>{{ $name }}</li>
        @endforeach
    </ul>

    <svg class="chart__svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {{ LineChart::WIDTH }} {{ LineChart::HEIGHT }}" role="group" aria-label="{{ $caption }}">
        @foreach ($chart->ticks as $tick)
            <line class="chart__grid" x1="{{ LineChart::LEFT }}" x2="{{ LineChart::RIGHT }}" y1="{{ $chart->y($tick) }}" y2="{{ $chart->y($tick) }}" />
            <text class="chart__text" x="{{ LineChart::LEFT - 10 }}" y="{{ $chart->y($tick) }}" text-anchor="end" dominant-baseline="middle">{{ $tick }}</text>
        @endforeach

        @foreach ($chart->labels as $index => $label)
            <text @class(['chart__text', 'chart__text--minor' => $chart->isMinorLabel($index)]) x="{{ $chart->x($index) }}" y="{{ LineChart::HEIGHT - 8 }}" text-anchor="middle">{{ $label }}</text>
        @endforeach

        <line class="chart__crosshair" x-ref="crosshair" y1="{{ LineChart::TOP }}" y2="{{ LineChart::BOTTOM }}" visibility="hidden" />

        @foreach ($chart->series as $name => $values)
            <path class="chart__line chart__line--{{ $loop->iteration }}" d="{{ $chart->path($values) }}" />
            @foreach ($values as $index => $value)
                <circle class="chart__dot chart__dot--{{ $loop->parent->iteration }}" cx="{{ $chart->x($index) }}" cy="{{ $chart->y($value) }}" r="4" />
            @endforeach
        @endforeach

        @foreach ($chart->labels as $index => $label)
            @php
                $rows = collect($chart->series)->values()->map(fn (array $values, int $seriesIndex): array => [
                    'label' => $seriesNames[$seriesIndex],
                    'value' => (string) $values[$index],
                    'series' => $seriesIndex + 1,
                ])->all();
                $summary = collect($rows)->map(fn (array $row): string => $row['value'].' '.$row['label'])->implode(', ');
            @endphp
            <rect
                class="chart__hit"
                x="{{ $chart->x($index) - $chart->columnWidth() / 2 }}"
                y="{{ LineChart::TOP }}"
                width="{{ $chart->columnWidth() }}"
                height="{{ LineChart::BOTTOM - LineChart::TOP }}"
                tabindex="0"
                role="img"
                aria-label="{{ $ranges[$index] }}: {{ $summary }}"
                data-x="{{ $chart->x($index) }}"
                data-tooltip="{{ json_encode(['title' => $ranges[$index], 'rows' => $rows]) }}"
            />
        @endforeach
    </svg>

    <div class="chart__tooltip" x-ref="tooltip" hidden></div>

    <table class="visually-hidden">
        <caption>{{ $caption }}</caption>
        <thead>
            <tr>
                <th scope="col">Period</th>
                @foreach ($seriesNames as $name)
                    <th scope="col">{{ $name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($chart->labels as $index => $label)
                <tr>
                    <th scope="row">{{ $ranges[$index] }}</th>
                    @foreach ($chart->series as $values)
                        <td>{{ $values[$index] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
