<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Tender;
use App\Models\TenderDocumentAttachment;
use App\Support\FileStorage;
use Illuminate\Http\Request;

class TenderDocumentAttachmentController extends ModuleController
{
    protected string $module = 'tenders';

    /** Accepts one or more {file} rows for one of the two document lists — see tenders/show.blade.php. */
    public function store(Request $request, Tender $tender)
    {
        $validated = $request->validate([
            'kind'                => ['required', 'in:documents,design'],
            'attachments'         => ['nullable', 'array'],
            'attachments.*.file'  => ['nullable', 'file', 'max:20480'],
        ]);

        foreach ($validated['attachments'] ?? [] as $row) {
            if (empty($row['file'])) {
                continue;
            }

            $tender->documentAttachments()->create([
                'kind'          => $validated['kind'],
                'path'          => FileStorage::store($row['file'], 'tenders/documents'),
                'original_name' => $row['file']->getClientOriginalName(),
                'created_by'    => $request->user()->id,
            ]);
        }

        return back()->with('success', __('tenders.document_attachment_added'));
    }

    public function destroy(Tender $tender, TenderDocumentAttachment $attachment)
    {
        abort_unless($attachment->tender_id === $tender->id, 404);

        FileStorage::delete($attachment->path);
        $attachment->delete();

        return back()->with('success', __('tenders.document_attachment_deleted'));
    }
}
