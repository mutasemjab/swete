<?php

namespace App\Http\Controllers\Tenders;

use App\Http\Controllers\ModuleController;
use App\Models\TenderStatus;
use Illuminate\Http\Request;

/** Dynamic/manageable list of tender statuses — "add anything in the future" without code changes. */
class TenderStatusController extends ModuleController
{
    protected string $module = 'tenders';

    private const COLORS = ['slate', 'blue', 'indigo', 'emerald', 'amber', 'rose', 'violet'];

    public function index()
    {
        $statuses = TenderStatus::orderBy('name')->get();
        return $this->moduleView('tenders.statuses.index', compact('statuses'));
    }

    public function create()
    {
        return $this->moduleView('tenders.statuses.create', ['colors' => self::COLORS]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        TenderStatus::create([
            ...$validated,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('tender-statuses.index')
            ->with('success', __('tenders.status_added'));
    }

    public function edit(TenderStatus $tenderStatus)
    {
        return $this->moduleView('tenders.statuses.edit', ['tenderStatus' => $tenderStatus, 'colors' => self::COLORS]);
    }

    public function update(Request $request, TenderStatus $tenderStatus)
    {
        $validated = $this->validated($request, $tenderStatus->id);

        $tenderStatus->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('tender-statuses.index')
            ->with('success', __('tenders.status_updated'));
    }

    public function destroy(TenderStatus $tenderStatus)
    {
        if ($tenderStatus->tenders()->exists()) {
            return back()->with('error', __('tenders.status_in_use'));
        }

        $tenderStatus->delete();

        return redirect()->route('tender-statuses.index')
            ->with('success', __('tenders.status_deleted'));
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code'    => ['required', 'string', 'max:50', 'alpha_dash', 'unique:tender_statuses,code' . ($ignoreId ? ",{$ignoreId}" : '')],
            'name'    => ['required', 'string', 'max:100'],
            'name_en' => ['nullable', 'string', 'max:100'],
            'color'   => ['required', 'in:' . implode(',', self::COLORS)],
        ]);
    }
}
