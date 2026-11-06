<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\ExportFormat;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use OpenSpout\Common\Entity\Row;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class TableExporter
{
    private const int CHUNK_SIZE = 200;

    /**
     * @param  Builder<*>  $query
     * @param  array<string, Closure(mixed): (string|int|float|bool|null)>  $columns  Value callbacks keyed by heading.
     */
    public function download(Builder $query, array $columns, string $name, ExportFormat $format): StreamedResponse
    {
        $filename = $name.'-'.today()->toDateString().'.'.$format->value;

        return response()->streamDownload(function () use ($query, $columns, $format): void {
            $writer = $format->writer();
            $writer->openToFile('php://output');
            $writer->addRow(Row::fromValues(array_keys($columns)));

            $model = $query->getModel();

            $query->lazyById(self::CHUNK_SIZE, $model->getQualifiedKeyName(), $model->getKeyName())
                ->each(function (Model $record) use ($writer, $columns, $format): void {
                    $values = array_map(fn (Closure $value) => self::cell($value($record), $format), array_values($columns));

                    $writer->addRow(Row::fromValues($values));
                });

            $writer->close();
        }, $filename, ['Content-Type' => $format->contentType()]);
    }

    /**
     * Spreadsheets run CSV cells that look like formulas, and these values are
     * user input. XLSX cells are stored as text, so they need no change.
     */
    private static function cell(string|int|float|bool|null $value, ExportFormat $format): string|int|float|bool|null
    {
        if ($format === ExportFormat::Csv && is_string($value) && preg_match('/^[=+\-@\t\r]/', $value) === 1) {
            return "'".$value;
        }

        return $value;
    }
}
