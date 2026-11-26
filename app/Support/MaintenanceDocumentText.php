<?php

namespace App\Support;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Config;
use Smalot\PdfParser\Parser;

class MaintenanceDocumentText
{
    public function pages(Document $document): array
    {
        if (! $document->path) {
            if (! \Illuminate\Support\Facades\Schema::hasTable('maintenance_import_records')) return [];
            return \Illuminate\Support\Facades\DB::table('maintenance_import_records')
                ->where('source_table', 'core_documentpage')->where('target_table', 'documents')->where('target_id', $document->id)
                ->pluck('payload')->map(function ($payload) {
                    $row = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
                    return ['number' => (int) $row['number'], 'text' => $row['text']];
                })->sortBy('number')->values()->all();
        }
        $key = self::cacheKey($document);
        if (Storage::disk('maintenance_private')->exists($key)) {
            return json_decode(Storage::disk('maintenance_private')->get($key), true, 512, JSON_THROW_ON_ERROR);
        }
        if (strtolower(pathinfo($document->original_name, PATHINFO_EXTENSION)) !== 'pdf' || $document->size > 20 * 1024 * 1024) {
            return [];
        }
        $config = new Config();
        $config->setDecodeMemoryLimit(32 * 1024 * 1024);
        $pdf = (new Parser([], $config))->parseContent(Storage::get($document->path));
        $pages = [];
        foreach ($pdf->getPages() as $index => $page) {
            $text = trim($page->getText());
            if ($text !== '') {
                $pages[] = ['number' => $index + 1, 'text' => mb_substr($text, 0, 50000)];
            }
        }
        Storage::disk('maintenance_private')->put($key, json_encode($pages, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        return $pages;
    }

    public static function cacheKey(Document $document): string
    {
        return 'maintenance-document-text/'.$document->id.'-'.sha1($document->path.'|'.$document->updated_at->toISOString()).'.json';
    }
}
