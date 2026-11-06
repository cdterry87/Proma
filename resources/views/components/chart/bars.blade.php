@props(['rows', 'caption'])

@php
    $width = 300;
    $rowHeight = 40;
    $labelWidth = 78;
    $valueWidth = 34;
    $barHeight = 18;
    $plotWidth = $width - $labelWidth - $valueWidth;
    $maximum = max(1, ...array_column($rows, 'value'));
    $total = array_sum(array_column($rows, 'value'));
@endphp

<div {{ $attributes->class('chart') }} x-data="chart" x-on:pointerover="show" x-on:focusin="show" x-on:pointerleave="hide" x-on:focusout="hide">
    <svg class="chart__svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {{ $width }} {{ $rowHeight * count($rows) }}" role="group" aria-label="{{ $caption }}">
        @foreach ($rows as $index => $row)
            @php
                $y = $rowHeight * $index;
                $middle = $y + $rowHeight / 2;
                $barWidth = $row['value'] > 0 ? max(4, round($plotWidth * $row['value'] / $maximum, 2)) : 0;
                $share = $total > 0 ? round($row['value'] / $total * 100) : 0;
            @endphp
            <text class="chart__text chart__text--strong" x="0" y="{{ $middle }}" dominant-baseline="middle">{{ $row['label'] }}</text>
            <line class="chart__grid" x1="{{ $labelWidth }}" x2="{{ $labelWidth }}" y1="{{ $y + 6 }}" y2="{{ $y + $rowHeight - 6 }}" />
            @if ($barWidth > 0)
                <path class="chart__bar" d="M{{ $labelWidth }} {{ $middle - $barHeight / 2 }} h{{ max(0, $barWidth - 4) }} a4 4 0 0 1 4 4 v{{ $barHeight - 8 }} a4 4 0 0 1 -4 4 h-{{ max(0, $barWidth - 4) }} z" />
            @endif
            <text class="chart__text chart__text--strong" x="{{ $labelWidth + $barWidth + 8 }}" y="{{ $middle }}" dominant-baseline="middle">{{ $row['value'] }}</text>
            <rect
                class="chart__hit"
                x="0"
                y="{{ $y }}"
                width="{{ $width }}"
                height="{{ $rowHeight }}"
                tabindex="0"
                role="img"
                aria-label="{{ $row['label'] }}: {{ $row['value'] }}, {{ $share }} percent"
                data-tooltip="{{ json_encode(['title' => $row['label'], 'rows' => [['label' => Str::plural('issue', $row['value']), 'value' => (string) $row['value']], ['label' => 'of total', 'value' => $share.'%']]]) }}"
            />
        @endforeach
    </svg>

    <div class="chart__tooltip" x-ref="tooltip" hidden></div>

    <table class="visually-hidden">
        <caption>{{ $caption }}</caption>
        <thead>
            <tr>
                <th scope="col">Priority</th>
                <th scope="col">Issues</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <th scope="row">{{ $row['label'] }}</th>
                    <td>{{ $row['value'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
