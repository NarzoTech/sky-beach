<?php

namespace Modules\Membership\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Membership\app\Models\LoyaltyProgram;
use Modules\Membership\app\Models\LoyaltyRule;
use Modules\Membership\app\Services\RuleEngineService;

class LoyaltyRuleController extends Controller
{
    protected $ruleEngineService;

    public function __construct(RuleEngineService $ruleEngineService)
    {
        $this->ruleEngineService = $ruleEngineService;
    }

    /**
     * Display rules for a program
     */
    public function index(Request $request): View
    {
        $programId = $request->query('program_id');

        $rules = LoyaltyRule::query()
            ->when($programId, fn ($q) => $q->where('loyalty_program_id', $programId))
            ->with('program', 'createdBy')
            ->orderByPriority()
            ->paginate(15);

        $programs = LoyaltyProgram::all();

        return view('membership::rules.index', [
            'rules' => $rules,
            'programs' => $programs,
            'selectedProgramId' => $programId,
        ]);
    }

    /**
     * Show the form for creating a new rule
     */
    public function create(Request $request): View
    {
        $programId = $request->query('program_id');
        $program = LoyaltyProgram::findOrFail($programId);

        return view('membership::rules.create', [
            'program' => $program,
        ]);
    }

    /**
     * Store a newly created rule
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'loyalty_program_id' => 'required|integer|exists:loyalty_programs,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'condition_type' => 'required|in:category,item,amount,time_period,customer_group',
            'condition_value' => 'nullable|json',
            'action_type' => 'required|in:earn_points,bonus_points,multiply_points,redeem_discount',
            'action_value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'day_of_week' => 'nullable|json',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'applies_to' => 'required|in:all,specific_items,specific_categories,specific_customers',
            'applicable_items' => 'nullable|json',
            'applicable_categories' => 'nullable|json',
            'applicable_customer_segments' => 'nullable|json',
            'priority' => 'integer|min:0|max:1000',
        ]);

        // Validate rule data
        $validation = $this->ruleEngineService->validateRuleData($validated);
        if (! $validation['valid']) {
            return redirect()->back()
                ->withErrors($validation['errors'])
                ->withInput();
        }

        $validated['created_by'] = auth()->id();

        LoyaltyRule::create($validated);

        return redirect()->route('membership.rules.index', ['program_id' => $validated['loyalty_program_id']])
            ->with('success', 'Rule created successfully');
    }

    /**
     * Display the specified rule
     */
    public function show(LoyaltyRule $rule): View
    {
        $rule->load('program', 'createdBy');

        return view('membership::rules.show', [
            'rule' => $rule,
        ]);
    }

    /**
     * Show the form for editing a rule
     */
    public function edit(LoyaltyRule $rule): View
    {
        return view('membership::rules.edit', [
            'rule' => $rule,
        ]);
    }

    /**
     * Update the specified rule
     */
    public function update(Request $request, LoyaltyRule $rule): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'condition_type' => 'required|in:category,item,amount,time_period,customer_group',
            'condition_value' => 'nullable|json',
            'action_type' => 'required|in:earn_points,bonus_points,multiply_points,redeem_discount',
            'action_value' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'day_of_week' => 'nullable|json',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'applies_to' => 'required|in:all,specific_items,specific_categories,specific_customers',
            'applicable_items' => 'nullable|json',
            'applicable_categories' => 'nullable|json',
            'applicable_customer_segments' => 'nullable|json',
            'priority' => 'integer|min:0|max:1000',
        ]);

        // Validate rule data
        $validation = $this->ruleEngineService->validateRuleData($validated);
        if (! $validation['valid']) {
            return redirect()->back()
                ->withErrors($validation['errors'])
                ->withInput();
        }

        $rule->update($validated);

        return redirect()->route('membership.rules.show', $rule)
            ->with('success', 'Rule updated successfully');
    }

    /**
     * Delete the specified rule
     */
    public function destroy(LoyaltyRule $rule): RedirectResponse
    {
        $programId = $rule->loyalty_program_id;
        $rule->delete();

        return redirect()->route('membership.rules.index', ['program_id' => $programId])
            ->with('success', 'Rule deleted successfully');
    }

    /**
     * Bulk update rule priorities
     */
    public function updatePriorities(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'priorities' => 'required|array',
            'priorities.*.id' => 'required|integer|exists:loyalty_rules,id',
            'priorities.*.priority' => 'required|integer',
        ]);

        foreach ($validated['priorities'] as $item) {
            LoyaltyRule::findOrFail($item['id'])->update(['priority' => $item['priority']]);
        }

        return redirect()->back()
            ->with('success', 'Rule priorities updated successfully');
    }
}
