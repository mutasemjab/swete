<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\ModuleController;
use App\Models\Party;
use App\Models\PartyGroup;
use Illuminate\Http\Request;

/** Shared controller for both customers (العملاء) and suppliers (الموردين). */
class PartyController extends ModuleController
{
    protected string $module = 'accounting';

    public function index(Request $request, string $type)
    {
        $query = Party::ofType($type)->with('group');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('name_en', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%"));
        }

        $parties = $query->orderBy('name')->paginate(20)->withQueryString();

        return $this->moduleView('accounting.parties.index', compact('parties', 'type'));
    }

    public function create(string $type)
    {
        $groups = PartyGroup::ofType($type)->orderBy('name')->get();
        return $this->moduleView('accounting.parties.create', compact('groups', 'type'));
    }

    public function store(Request $request, string $type)
    {
        $validated = $this->validated($request);

        Party::create([
            ...$validated,
            'type'   => $type,
            'code'   => Party::nextCode($type),
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route("accounting.{$type}s.index")
            ->with('success', __('accounting.party_added'));
    }

    public function edit(string $type, Party $party)
    {
        $groups = PartyGroup::ofType($type)->orderBy('name')->get();
        return $this->moduleView('accounting.parties.edit', compact('party', 'groups', 'type'));
    }

    public function update(Request $request, string $type, Party $party)
    {
        $validated = $this->validated($request);

        $party->update([
            ...$validated,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route("accounting.{$type}s.index")
            ->with('success', __('accounting.party_updated'));
    }

    public function destroy(string $type, Party $party)
    {
        $party->delete();

        return redirect()->route("accounting.{$type}s.index")
            ->with('success', __('accounting.party_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'party_group_id'  => ['nullable', 'exists:party_groups,id'],
            'name'            => ['required', 'string', 'max:150'],
            'name_en'         => ['nullable', 'string', 'max:150'],
            'phone'           => ['nullable', 'string', 'max:30'],
            'email'           => ['nullable', 'email', 'max:150'],
            'address'         => ['nullable', 'string', 'max:255'],
            'tax_number'      => ['nullable', 'string', 'max:50'],
            'opening_balance' => ['nullable', 'numeric'],
            'status'          => ['boolean'],
        ]);
    }
}
