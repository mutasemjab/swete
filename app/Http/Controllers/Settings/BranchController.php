<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\ModuleController;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends ModuleController
{
    protected string $module = 'settings';

    /** Every image field this form accepts — the `{field}_path` DB column is derived from each. */
    private const IMAGE_FIELDS = [
        'logo', 'logo_secondary',
        'quote_header_image1', 'quote_header_image2', 'quote_header_image3',
        'quote_body_image1', 'quote_body_image2',
    ];

    public function index()
    {
        $branches = Branch::orderByDesc('is_main')->orderBy('name')->get();
        return $this->moduleView('settings.branches.index', compact('branches'));
    }

    public function create()
    {
        return $this->moduleView('settings.branches.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        if ($request->boolean('is_main')) {
            Branch::where('is_main', true)->update(['is_main' => false]);
        }

        $data = collect($validated)->except(self::IMAGE_FIELDS)->all();

        foreach (self::IMAGE_FIELDS as $field) {
            $data["{$field}_path"] = $this->storeImage($request, $field);
        }

        Branch::create([
            ...$data,
            'is_main' => $request->boolean('is_main'),
            'status'  => $request->boolean('status', true),
        ]);

        return redirect()->route('settings.branches.index')
            ->with('success', __('settings.branch_added'));
    }

    public function edit(Branch $branch)
    {
        return $this->moduleView('settings.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $this->validated($request);

        if ($request->boolean('is_main') && ! $branch->is_main) {
            Branch::where('is_main', true)->update(['is_main' => false]);
        }

        $data = collect($validated)->except(self::IMAGE_FIELDS)->all();

        foreach (self::IMAGE_FIELDS as $field) {
            if ($path = $this->storeImage($request, $field)) {
                $this->deleteImageFile($branch->{"{$field}_path"});
                $data["{$field}_path"] = $path;
            }
        }

        $branch->update([
            ...$data,
            'is_main' => $request->boolean('is_main'),
            'status'  => $request->boolean('status'),
        ]);

        return redirect()->route('settings.branches.index')
            ->with('success', __('settings.branch_updated'));
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->route('settings.branches.index')
            ->with('success', __('settings.branch_deleted'));
    }

    private function validated(Request $request): array
    {
        $rules = [
            'name'              => ['required', 'string', 'max:100'],
            'name_en'           => ['nullable', 'string', 'max:100'],
            'phone'             => ['nullable', 'string', 'max:30'],
            'fax'               => ['nullable', 'string', 'max:30'],
            'address_line1'     => ['nullable', 'string', 'max:255'],
            'address_line1_en'  => ['nullable', 'string', 'max:255'],
            'po_box'            => ['nullable', 'string', 'max:30'],
            'postal_code'       => ['nullable', 'string', 'max:30'],
            'city'              => ['nullable', 'string', 'max:100'],
            'city_en'           => ['nullable', 'string', 'max:100'],
            'country'           => ['nullable', 'string', 'max:100'],
            'country_en'        => ['nullable', 'string', 'max:100'],
            'is_main'           => ['boolean'],
            'status'            => ['boolean'],
        ];

        foreach (self::IMAGE_FIELDS as $field) {
            $rules[$field] = ['nullable', 'image', 'max:2048'];
        }

        return $request->validate($rules);
    }

    private function storeImage(Request $request, string $field): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        $filename = uploadImage('assets/uploads/branches', $request->file($field));

        return 'assets/uploads/branches/' . $filename;
    }

    private function deleteImageFile(?string $path): void
    {
        if ($path && file_exists(base_path($path))) {
            @unlink(base_path($path));
        }
    }
}
