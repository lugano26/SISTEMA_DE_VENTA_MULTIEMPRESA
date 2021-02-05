<?php

namespace App\Exports;

use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportGeneratorExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents, Responsable
{
    use Exportable;
    
    private $fileName = 'report-generated.xlsx';
    
    private $writerType = Excel::XLSX;
    
    private $headers = [];

    private $headersColumns = [];

    private $data = [];

    private $afterSheetEvent = null;

    public function __construct($headers, $data, $fileName = null, $afterEvent = null, $responseHeaders = null, $fileType = null)
    {
        $this->headersColumns = $headers;
        $this->data = $data;
        $this->fileName = $fileName ?: $this->fileName;
        $this->headers = $responseHeaders ?: $this->headers;
        $this->writerType = $fileType ?: $this->writerType;
        $this->afterSheetEvent = $afterEvent ?: function(AfterSheet $event) {};
    }
    
    public function setAfterEvent($event)
    {
        $this->afterSheetEvent = $event;
    }

    public function headings(): array
    {
        return  $this->headersColumns;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class  => function(BeforeExport $event) {
                $event->writer->setCreator('https://web.sysef.com/');
            },
            AfterSheet::class    =>  $this->afterSheetEvent,
        ];
    }
}
