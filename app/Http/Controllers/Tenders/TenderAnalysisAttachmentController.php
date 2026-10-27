<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\Tender;
use App\Models\TenderAnalysisAttachment;
use Illuminate\Http\Request;

class TenderAnalysisAttachmentController extends ModuleController
{
    protected string $module = 'tenders';

    /** Accepts one or more {name, file} rows in a single submit — mirrors ProjectAttachmentController::store(). */
    public function store(Request $request, Tender $tender)
    {
        $validated = $request->validate([
            'attachments'          => ['nullable', 'array'],
            'attachments.*.name'   => ['nullable', 'string', 'max:150'],
            'attachments.*.file'   => ['nullable', 'file', 'max:10240'],
        ]);

        foreach ($validated['attachments'] ?? [] as $row) {
            if (empty($row['file'])) {
                continue;
            }

            $filename = uploadImage('assets/uploads/tenders', $row['file']);

            $tender->analysisAttachments()->create([
                'name'       => $row['name'] ?: $row['file']->getClientOriginalName(),
                'path'       => 'assets/uploads/tenders/' . $filename,
                'created_by' => $request->user()->id,
            ]);
        }

        return back()->with('success', __('tenders.analysis_attachment_added'));
    }

    public function destroy(Tender $tender, TenderAnalysisAttachment $attachment)
    {
        abort_unless($attachment->tender_id === $tender->id, 404);

        if (file_exists(base_path($attachment->path))) {
            @unlink(base_path($attachment->path));
        }

        $attachment->delete();

        return back()->with('success', __('tenders.analysis_attachment_deleted'));
    }
}
