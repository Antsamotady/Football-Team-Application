<?php

namespace App\Service;

class CsvExporter
{
    private string $delimiter = ';';
    private bool $includeBom = true;

    /**
     * @param list<list<string|int|float|null>> $data
     * @param list<string> $headers
     */
    public function export(array $data, array $headers = []): string
    {
        $output = fopen('php://temp', 'r+');

        if ($output === false) {
            throw new \RuntimeException('Unable to open temporary memory stream');
        }

        if ($this->includeBom) {
            fwrite($output, "\xEF\xBB\xBF");
        }

        if ($headers !== []) {
            fputcsv($output, $headers, $this->delimiter);
        }

        foreach ($data as $row) {
            fputcsv($output, $row, $this->delimiter);
        }

        rewind($output);

        $csv = stream_get_contents($output);

        if ($csv === false) {
            fclose($output);
            throw new \RuntimeException('Unable to read CSV contents');
        }

        fclose($output);

        return $csv;
    }

    public function setDelimiter(string $delimiter): self
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    public function setIncludeBom(bool $includeBom): self
    {
        $this->includeBom = $includeBom;
        return $this;
    }
}