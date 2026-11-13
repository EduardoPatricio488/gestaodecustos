<?php

namespace App\Livewire;

use App\Models\Debt;
use App\Models\CalendarEventOverride;
use App\Models\CalendarVacation;
use App\Models\BankAccount;
use App\Models\BankTransfer;
use App\Models\DailyMealAllowance;
use App\Models\Expense;
use App\Models\FitnessActivity;
use App\Models\Income;
use App\Models\RecurringIncome;
use App\Models\Reminder;
use App\Models\SalaryPayment;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Services\BusinessAccessService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app')]
class PersonalCalendar extends Component
{
    private const MONTHLY_WORKING_DAYS = 22;

    public $month;

    public $year;

    public bool $showDayDetailModal = false;

    public bool $showVacationModal = false;

    public ?string $selectedDayKey = null;

    public ?string $editingEventKey = null;

    public array $eventEditForm = [];

    public array $mealAllowanceForms = [];

    public array $vacationForm = [];

    public array $activeFilters = [];

    public string $search = '';

    private const FILTER_TYPE_MAP = [
        'incomes' => ['income', 'salary'],
        'expenses' => ['expense'],
        'subscriptions' => ['subscription'],
        'transfers' => ['transfer'],
        'debts' => ['debt'],
        'fitness' => ['fitness'],
        'reminders' => ['reminder'],
    ];

    public function mount()
    {
        $this->month = now()->month;
        $this->year = now()->year;
    }

    public function toggleFilter(string $key)
    {
        if (in_array($key, $this->activeFilters, true)) {
            $this->activeFilters = array_values(array_diff($this->activeFilters, [$key]));
        } else {
            $this->activeFilters[] = $key;
        }
    }

    public function openDayDetail(string $dateKey)
    {
        $this->selectedDayKey = $dateKey;
        $this->showDayDetailModal = true;
        $this->populateMealAllowanceForms($dateKey);
    }

    public function closeDayDetail()
    {
        $this->showDayDetailModal = false;
        $this->editingEventKey = null;
    }

    public function editBankTransfer(int $transferId): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas utilizadores com permissões de edição podem alterar transferências.');

            return;
        }

        $this->closeDayDetail();
        $this->dispatch('calendar-bank-transfer-edit', transferId: $transferId);
        $this->eventEditForm = [];
        $this->mealAllowanceForms = [];
    }

    #[On('dashboard-record-created')]
    public function refreshCalendarAfterCreate(): void {}

    public function beginEditEvent(string $eventKey): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas utilizadores com permissões de edição podem alterar registos.');

            return;
        }

        $event = $this->selectedDayEvents->firstWhere('event_key', $eventKey);
        abort_unless($event, 404);
        abort_unless($event['editable'] ?? false, 403);

        $this->editingEventKey = $eventKey;
        $this->eventEditForm = [
            'date' => $event['date'],
            'label' => $event['label'],
            'amount' => $event['edit_amount'] ?? $event['amount'] ?? '',
            'duration_minutes' => $event['duration_minutes'] ?? '',
            'is_completed' => $event['done'] ?? false,
            'start_date' => $event['vacation_start_date'] ?? '',
            'end_date' => $event['vacation_end_date'] ?? '',
            'notes' => $event['notes'] ?? '',
        ];
        $this->resetValidation();
    }

    public function cancelEditEvent(): void
    {
        $this->editingEventKey = null;
        $this->eventEditForm = [];
        $this->resetValidation();
    }

    public function beginEditMealAllowance(int $contractId, string $dateKey): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas utilizadores com permissões de edição podem alterar o subsídio diário.');

            return;
        }

        abort_unless($this->selectedDayKey === $dateKey, 404);
        $event = $this->selectedDayEvents->firstWhere('event_key', 'meal_allowance:'.$contractId.':'.$dateKey);
        abort_unless($event, 404);
        $this->editingEventKey = $event['event_key'];
        $this->resetValidation();
    }

    public function saveEvent(string $eventKey): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas utilizadores com permissões de edição podem alterar registos.');

            return;
        }

        abort_unless($this->editingEventKey === $eventKey, 404);
        $event = $this->selectedDayEvents->firstWhere('event_key', $eventKey);
        abort_unless($event && ($event['editable'] ?? false), 404);

        $subtype = $event['subtype'] ?? null;
        $isVacation = $subtype === 'vacation';
        $isFitness = $event['type'] === 'fitness';
        $rules = [
            'eventEditForm.label' => $isVacation ? 'nullable|string|max:255' : 'required|string|max:255',
            'eventEditForm.date' => 'required|date',
            'eventEditForm.notes' => 'nullable|string|max:1000',
        ];

        if ($isVacation) {
            $rules['eventEditForm.start_date'] = 'required|date';
            $rules['eventEditForm.end_date'] = 'required|date|after_or_equal:eventEditForm.start_date';
        } elseif ($isFitness) {
            $rules['eventEditForm.duration_minutes'] = 'required|integer|min:1|max:1440';
        } elseif (isset($event['amount'])) {
            $minimumAmount = in_array($event['type'], ['expense', 'income', 'debt'], true) ? 0.01 : 0;
            $rules['eventEditForm.amount'] = 'required|numeric|min:'.$minimumAmount.'|max:99999999';
        }

        if ($event['type'] === 'reminder' && ! in_array($subtype, ['public_holiday', 'work_milestone'], true)) {
            $rules['eventEditForm.is_completed'] = 'boolean';
        }

        $this->resetValidation();
        $this->validate($rules);

        $workspaceId = auth()->user()->current_workspace_id;
        $userId = auth()->id();
        $date = Carbon::parse($this->eventEditForm['date'])->toDateString();
        $targetDate = $date;

        if ($subtype === 'vacation') {
            $startDate = Carbon::parse($this->eventEditForm['start_date'])->toDateString();
            $endDate = Carbon::parse($this->eventEditForm['end_date'])->toDateString();
            $overlapsExistingPeriod = CalendarVacation::where('workspace_id', $workspaceId)
                ->where('user_id', $userId)
                ->whereKeyNot($event['vacation_id'])
                ->whereDate('start_date', '<=', $endDate)
                ->whereDate('end_date', '>=', $startDate)
                ->exists();

            if ($overlapsExistingPeriod) {
                $this->addError('eventEditForm.start_date', 'Este período sobrepõe férias já registadas.');

                return;
            }

            CalendarVacation::where('workspace_id', $workspaceId)
                ->where('user_id', $userId)
                ->findOrFail($event['vacation_id'])
                ->update([
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'notes' => trim($this->eventEditForm['notes'] ?? '') ?: null,
                ]);
            $targetDate = $date >= $startDate && $date <= $endDate ? $date : $startDate;
        } elseif ($subtype === 'meal_allowance') {
            abort(404);
        } elseif ($event['type'] === 'expense') {
            $expense = Expense::where('workspace_id', $workspaceId)
                ->where('user_id', $userId)
                ->findOrFail($event['expense_id']);
            $amount = round((float) $this->eventEditForm['amount'], 2);

            if ($amount < (float) $expense->amount_paid || $amount < (float) $expense->vat_amount) {
                $this->addError('eventEditForm.amount', 'O valor não pode ser inferior ao montante já pago ou ao IVA registado.');

                return;
            }

            if ($expense->bank_account_id) {
                $account = BankAccount::where('workspace_id', $workspaceId)
                    ->where('user_id', $userId)
                    ->find($expense->bank_account_id);

                if ($account) {
                    $available = (float) $account->current_balance + (float) $expense->amount;
                    if ($amount > $available) {
                        $this->addError('eventEditForm.amount', 'Saldo insuficiente na conta bancária associada.');

                        return;
                    }
                }
            }

            $expense->update([
                'description' => $this->eventEditForm['label'],
                'spent_at' => $date,
                'amount' => $amount,
            ]);
        } elseif ($event['type'] === 'income') {
            Income::where('workspace_id', $workspaceId)
                ->where('user_id', $userId)
                ->findOrFail($event['income_id'])
                ->update([
                    'description' => $this->eventEditForm['label'],
                    'received_at' => $date,
                    'amount' => round((float) $this->eventEditForm['amount'], 2),
                ]);
        } elseif ($event['type'] === 'subscription') {
            Subscription::where('workspace_id', $workspaceId)
                ->where('user_id', $userId)
                ->findOrFail($event['subscription_id'])
                ->update([
                    'name' => $this->eventEditForm['label'],
                    'billing_day' => Carbon::parse($date)->day,
                    'amount' => round((float) $this->eventEditForm['amount'], 2),
                ]);
        } elseif ($event['type'] === 'debt') {
            Debt::where('workspace_id', $workspaceId)
                ->where('user_id', $userId)
                ->findOrFail($event['debt_id'])
                ->update([
                    'description' => $this->eventEditForm['label'],
                    'due_at' => $date,
                    'amount' => round((float) $this->eventEditForm['amount'], 2),
                ]);
        } elseif ($isFitness) {
            FitnessActivity::where('workspace_id', $workspaceId)
                ->where('user_id', $userId)
                ->findOrFail($event['fitness_id'])
                ->update([
                    'type' => $this->eventEditForm['label'],
                    'activity_date' => $date,
                    'duration_minutes' => (int) $this->eventEditForm['duration_minutes'],
                ]);
        } elseif ($event['type'] === 'reminder' && $subtype === null) {
            $reminder = Reminder::where('workspace_id', $workspaceId)
                ->where('user_id', $userId)
                ->findOrFail($event['reminder_id']);
            $reminderTime = $reminder->remind_at->format('H:i:s');
            $reminder->update([
                'title' => $this->eventEditForm['label'],
                'remind_at' => $date.' '.$reminderTime,
                'is_completed' => (bool) ($this->eventEditForm['is_completed'] ?? false),
            ]);
        } else {
            $eventData = array_replace($event, [
                'date' => $date,
                'label' => $this->eventEditForm['label'],
            ]);

            if (isset($event['amount'])) {
                $eventData['amount'] = round((float) $this->eventEditForm['amount'], 2);
                $eventData['edit_amount'] = $eventData['amount'];
            }

            CalendarEventOverride::updateOrCreate(
                [
                    'workspace_id' => $workspaceId,
                    'user_id' => $userId,
                    'event_key' => $eventKey,
                ],
                [
                    'event_date' => $date,
                    'event_data' => $eventData,
                ],
            );
        }

        $this->month = Carbon::parse($targetDate)->month;
        $this->year = Carbon::parse($targetDate)->year;
        $this->selectedDayKey = $targetDate;
        $this->editingEventKey = null;
        $this->eventEditForm = [];
        unset(
            $this->dayEvents,
            $this->vacationPeriods,
            $this->visibleDayEvents,
            $this->selectedDayEvents,
            $this->selectedDaySummary,
        );
        $this->populateMealAllowanceForms($targetDate);
        $this->dispatch('toast', text: 'Registo atualizado.');
    }

    public function saveMealAllowanceDay(int $contractId, string $dateKey): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas utilizadores com permissões de edição podem alterar o subsídio diário.');

            return;
        }

        abort_unless($this->selectedDayKey === $dateKey, 404);
        $contract = $this->mealAllowanceContract($contractId);
        $date = $this->parseSelectedCalendarDate($dateKey);
        abort_unless(
            $this->mealAllowanceScheduleDates(
                $contract,
                Carbon::create($this->year, $this->month, 1),
                $this->vacationDateKeys(),
            )->contains($dateKey),
            404,
        );

        $formKey = 'mealAllowanceForms.'.$contractId;
        $this->resetValidation();
        $this->validate([
            $formKey.'.received_amount' => 'required|numeric|min:0|max:99999999',
            $formKey.'.notes' => 'nullable|string|max:1000',
        ]);

        $form = $this->mealAllowanceForms[$contractId];

        $record = DailyMealAllowance::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->where('recurring_income_id', $contract->id)
            ->whereDate('allowance_date', $date->toDateString())
            ->first();
        $expectedAmount = $record
            ? (float) $record->expected_amount
            : $this->mealAllowanceDailyAmount($contract);

        DailyMealAllowance::updateOrCreate(
            [
                'workspace_id' => auth()->user()->current_workspace_id,
                'user_id' => auth()->id(),
                'recurring_income_id' => $contract->id,
                'allowance_date' => $date->toDateString(),
            ],
            [
                'expected_amount' => $expectedAmount,
                'received_amount' => round((float) $form['received_amount'], 2),
                'notes' => trim($form['notes'] ?? '') ?: null,
            ],
        );

        unset($this->dayEvents);
        $this->populateMealAllowanceForms($dateKey);
        $this->editingEventKey = null;
        $this->dispatch('toast', text: 'Registo diário do subsídio atualizado.');
    }

    private function populateMealAllowanceForms(string $dateKey): void
    {
        $this->mealAllowanceForms = $this->dayEvents
            ->get($dateKey, collect())
            ->where('subtype', 'meal_allowance')
            ->mapWithKeys(fn (array $event): array => [
                $event['contract_id'] => [
                    'received_amount' => number_format((float) $event['amount'], 2, '.', ''),
                    'notes' => $event['notes'] ?? '',
                ],
            ])
            ->all();
    }

    private function mealAllowanceContract(int $contractId): RecurringIncome
    {
        $contract = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->where('source', 'emprego')
            ->findOrFail($contractId);

        abort_unless(
            $contract->is_active
                && ($contract->metadata['contract_status'] ?? 'active') === 'active'
                && ($contract->metadata['meal_allowance_destination'] ?? null) === 'meal_card',
            404,
        );

        return $contract;
    }

    public function openVacationModal(): void
    {
        $today = now()->toDateString();
        $this->vacationForm = [
            'start_date' => $today,
            'end_date' => $today,
            'notes' => '',
        ];
        $this->resetValidation();
        $this->showVacationModal = true;
    }

    public function closeVacationModal(): void
    {
        $this->showVacationModal = false;
        $this->resetValidation();
    }

    #[Computed]
    public function vacationSummary(): ?array
    {
        $startDate = $this->parseVacationDate($this->vacationForm['start_date'] ?? null);
        $endDate = $this->parseVacationDate($this->vacationForm['end_date'] ?? null);

        if (! $startDate || ! $endDate || $endDate->lessThan($startDate)) {
            return null;
        }

        $calendarDays = (int) $startDate->diffInDays($endDate) + 1;
        $weekdayDays = $this->countWeekdays($startDate, $endDate);
        $holidayDays = count($this->publicHolidayDateKeysBetween($startDate, $endDate));
        $workingDays = $this->countWorkingDays($startDate, $endDate);
        $weekendDays = $calendarDays - $weekdayDays;
        $mealAllowanceContracts = RecurringIncome::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->where('source', 'emprego')
            ->where('is_active', true)
            ->get()
            ->reject(fn (RecurringIncome $income) => ($income->metadata['income_type'] ?? null) === 'meal_card_allowance')
            ->filter(fn (RecurringIncome $income) => ($income->metadata['contract_status'] ?? 'active') === 'active'
                && ($income->metadata['meal_allowance_destination'] ?? null) === 'meal_card');
        $foregoneAllowance = 0.0;

        foreach ($mealAllowanceContracts as $contract) {
            $metadata = $contract->metadata ?? [];
            $contractStart = ! empty($metadata['start_date'])
                ? Carbon::parse($metadata['start_date'])->startOfDay()
                : $contract->created_at?->copy()->startOfDay() ?? $startDate->copy();
            $contractEnd = ! empty($metadata['end_date'])
                ? Carbon::parse($metadata['end_date'])->endOfDay()
                : $endDate->copy()->endOfDay();
            $eligibleStart = $startDate->greaterThan($contractStart) ? $startDate : $contractStart;
            $eligibleEnd = $endDate->lessThan($contractEnd) ? $endDate : $contractEnd;

            if ($eligibleEnd->lessThan($eligibleStart)) {
                continue;
            }

            $eligibleWorkingDays = $this->countWorkingDays($eligibleStart, $eligibleEnd);
            $foregoneAllowance += $eligibleWorkingDays * $this->mealAllowanceDailyAmount($contract);
        }

        return [
            'calendar_days' => $calendarDays,
            'working_days' => $workingDays,
            'weekend_days' => $weekendDays,
            'holiday_days' => $holidayDays,
            'foregone_allowance' => round($foregoneAllowance, 2),
            'has_meal_allowance' => $mealAllowanceContracts->isNotEmpty(),
        ];
    }

    private function parseVacationDate(mixed $value): ?Carbon
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = \DateTimeImmutable::getLastErrors();

        if (
            ! $date
            || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))
            || $date->format('Y-m-d') !== $value
        ) {
            return null;
        }

        return Carbon::parse($value)->startOfDay();
    }

    private function countWeekdays(Carbon $startDate, Carbon $endDate): int
    {
        $calendarDays = (int) $startDate->diffInDays($endDate) + 1;
        $fullWeeks = intdiv($calendarDays, 7);
        $workingDays = $fullWeeks * 5;

        for ($offset = 0; $offset < $calendarDays % 7; $offset++) {
            if ($startDate->copy()->addDays($offset)->isWeekday()) {
                $workingDays++;
            }
        }

        return $workingDays;
    }

    private function countWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        return max(0, $this->countWeekdays($startDate, $endDate) - $this->countPublicHolidayWeekdays($startDate, $endDate));
    }

    private function countPublicHolidayWeekdays(Carbon $startDate, Carbon $endDate): int
    {
        return collect(array_keys($this->publicHolidayDateKeysBetween($startDate, $endDate)))
            ->filter(fn (string $date): bool => Carbon::parse($date)->isWeekday())
            ->count();
    }

    private function publicHolidayDateKeysBetween(Carbon $startDate, Carbon $endDate): array
    {
        $dateKeys = [];

        for ($year = $startDate->year; $year <= $endDate->year; $year++) {
            foreach (array_keys($this->portugalPublicHolidays($year)) as $dateKey) {
                if ($dateKey >= $startDate->toDateString() && $dateKey <= $endDate->toDateString()) {
                    $dateKeys[$dateKey] = true;
                }
            }
        }

        return $dateKeys;
    }

    private function publicHolidayDateKeys(int $year): array
    {
        return array_fill_keys(array_keys($this->portugalPublicHolidays($year)), true);
    }

    private function portugalPublicHolidays(int $year): array
    {
        $holidays = [];
        $addHoliday = function (Carbon $date, string $label, string $scope) use (&$holidays): void {
            $holidays[$date->toDateString()][] = [
                'label' => $label,
                'scope' => $scope,
            ];
        };
        $nationalHolidays = [
            [1, 1, 'Ano Novo'],
            [4, 25, 'Dia da Liberdade'],
            [5, 1, 'Dia do Trabalhador'],
            [6, 10, 'Dia de Portugal, de Camões e das Comunidades Portuguesas'],
            [8, 15, 'Assunção de Nossa Senhora'],
            [10, 5, 'Implantação da República'],
            [11, 1, 'Todos os Santos'],
            [12, 1, 'Restauração da Independência'],
            [12, 8, 'Imaculada Conceição'],
            [12, 25, 'Natal'],
        ];

        foreach ($nationalHolidays as [$month, $day, $label]) {
            $addHoliday(Carbon::create($year, $month, $day), $label, 'Feriado nacional');
        }

        $easter = $this->easterSunday($year);
        $addHoliday($easter->copy()->subDays(2), 'Sexta-feira Santa', 'Feriado nacional');
        $addHoliday($easter, 'Domingo de Páscoa', 'Feriado nacional');
        $addHoliday($easter->copy()->addDays(39), 'Quinta-feira da Ascensão', 'Feriado municipal · Mafra');
        $addHoliday($easter->copy()->addDays(60), 'Corpo de Deus', 'Feriado nacional');
        $addHoliday(Carbon::create($year, 6, 13), 'Santo António', 'Feriado municipal · Lisboa');

        return $holidays;
    }

    private function easterSunday(int $year): Carbon
    {
        $a = $year % 19;
        $b = intdiv($year, 100);
        $c = $year % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $month = intdiv($h + $l - 7 * $m + 114, 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::create($year, $month, $day)->startOfDay();
    }

    private function publicHolidayEvents(Carbon $start, Carbon $end): \Illuminate\Support\Collection
    {
        $events = collect();

        foreach ($this->portugalPublicHolidays($start->year) as $date => $holidays) {
            if ($date < $start->toDateString() || $date > $end->toDateString()) {
                continue;
            }

            foreach ($holidays as $holidayIndex => $holiday) {
                $events->push([
                    'event_key' => 'public_holiday:'.$date.':'.$holidayIndex,
                    'editable' => true,
                    'date' => $date,
                    'type' => 'reminder',
                    'subtype' => 'public_holiday',
                    'label' => $holiday['label'],
                    'meta' => $holiday['scope'],
                    'color' => 'text-amber-500',
                ]);
            }
        }

        return $events;
    }

    private function calendarEventIndicator(array $event): array
    {
        if (($event['subtype'] ?? null) === 'meal_allowance') {
            return ['calendar_icon' => 'cake', 'calendar_icon_label' => 'Subsídio de alimentação'];
        }

        if (($event['subtype'] ?? null) === 'payday') {
            return ['calendar_icon' => 'calendar-days', 'calendar_icon_label' => 'Dia de pagamento'];
        }

        if ($event['type'] === 'reminder') {
            return match ($event['subtype'] ?? null) {
                'public_holiday' => ['calendar_icon' => 'calendar-days', 'calendar_icon_label' => 'Feriado'],
                'work_milestone' => ['calendar_icon' => 'briefcase', 'calendar_icon_label' => 'Marco de trabalho'],
                'vacation' => ['calendar_icon' => 'sun', 'calendar_icon_label' => 'Férias'],
                default => ['calendar_icon' => 'bell', 'calendar_icon_label' => 'Lembrete'],
            };
        }

        return match ($event['type']) {
            'income' => ['calendar_icon' => 'banknotes', 'calendar_icon_label' => 'Receita'],
            'salary' => ['calendar_icon' => 'banknotes', 'calendar_icon_label' => 'Receita prevista'],
            'expense' => ['calendar_icon' => 'credit-card', 'calendar_icon_label' => 'Despesa'],
            'subscription' => ['calendar_icon' => 'arrow-path', 'calendar_icon_label' => 'Assinatura'],
            'transfer' => ['calendar_icon' => 'arrows-right-left', 'calendar_icon_label' => 'Transferência'],
            'debt' => ['calendar_icon' => 'banknotes', 'calendar_icon_label' => 'Parcela'],
            'fitness' => ['calendar_icon' => 'bolt', 'calendar_icon_label' => 'Treino'],
            default => ['calendar_icon' => null, 'calendar_icon_label' => null],
        };
    }

    public function saveVacationPeriod(): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas utilizadores com permissões de edição podem registar férias.');

            return;
        }

        $this->resetValidation();
        $this->validate([
            'vacationForm.start_date' => 'required|date',
            'vacationForm.end_date' => 'required|date|after_or_equal:vacationForm.start_date',
            'vacationForm.notes' => 'nullable|string|max:1000',
        ]);

        $startDate = Carbon::parse($this->vacationForm['start_date'])->toDateString();
        $endDate = Carbon::parse($this->vacationForm['end_date'])->toDateString();
        $overlapsExistingPeriod = CalendarVacation::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->exists();

        if ($overlapsExistingPeriod) {
            $this->addError('vacationForm.start_date', 'Este período sobrepõe férias já registadas.');

            return;
        }

        CalendarVacation::create([
            'workspace_id' => auth()->user()->current_workspace_id,
            'user_id' => auth()->id(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'notes' => trim($this->vacationForm['notes'] ?? '') ?: null,
        ]);

        $this->showVacationModal = false;
        unset($this->dayEvents, $this->vacationPeriods);
        $this->dispatch('toast', text: 'Período de férias registado. O subsídio diário não será previsto nesses dias.');
    }

    public function deleteVacationPeriod(int $vacationId): void
    {
        if (auth()->user()->isViewer()) {
            $this->dispatch('toast', variant: 'error', text: 'Apenas utilizadores com permissões de edição podem remover férias.');

            return;
        }

        CalendarVacation::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->findOrFail($vacationId)
            ->delete();

        unset($this->dayEvents, $this->vacationPeriods);
        $this->dispatch('toast', text: 'Período de férias removido.');
    }

    #[Computed]
    public function vacationPeriods()
    {
        $start = Carbon::create($this->year, $this->month, 1)->startOfMonth();

        return $this->vacationPeriodsForMonth($start);
    }

    private function vacationPeriodsForMonth(Carbon $start): Collection
    {
        $end = $start->copy()->endOfMonth();

        return CalendarVacation::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString())
            ->orderBy('start_date')
            ->get();
    }

    private function vacationDateKeys(?Carbon $monthStart = null, ?Collection $periods = null): array
    {
        $monthStart = $monthStart?->copy()->startOfMonth() ?? Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $dateKeys = [];

        foreach ($periods ?? $this->vacationPeriods as $period) {
            $periodStart = $period->start_date->greaterThan($monthStart) ? $period->start_date->copy() : $monthStart->copy();
            $periodEnd = $period->end_date->lessThan($monthEnd) ? $period->end_date->copy() : $monthEnd->copy();

            for ($date = $periodStart; $date->lessThanOrEqualTo($periodEnd); $date = $date->addDay()) {
                $dateKeys[$date->toDateString()] = true;
            }
        }

        return $dateKeys;
    }

    private function parseSelectedCalendarDate(string $dateKey): Carbon
    {
        $date = Carbon::createFromFormat('!Y-m-d', $dateKey);
        abort_unless(
            $date
                && $date->format('Y-m-d') === $dateKey
                && (int) $date->year === (int) $this->year
                && (int) $date->month === (int) $this->month,
            404,
        );

        return $date;
    }

    private function mealAllowanceScheduleDates(RecurringIncome $income, Carbon $monthStart, array $excludedDateKeys = []): \Illuminate\Support\Collection
    {
        $metadata = $income->metadata ?? [];
        $monthStart = $monthStart->copy()->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();
        $contractStart = ! empty($metadata['start_date'])
            ? Carbon::parse($metadata['start_date'])->startOfDay()
            : $income->created_at?->copy()->startOfDay() ?? $monthStart->copy();
        $contractEnd = ! empty($metadata['end_date'])
            ? Carbon::parse($metadata['end_date'])->endOfDay()
            : $monthEnd->copy();
        $rangeStart = $contractStart->greaterThan($monthStart) ? $contractStart : $monthStart;
        $rangeEnd = $contractEnd->lessThan($monthEnd) ? $contractEnd : $monthEnd;

        if ($rangeEnd->lessThan($rangeStart)) {
            return collect();
        }

        $workingDays = max(0, (int) ($metadata['working_days'] ?? 0));
        $publicHolidayDateKeys = $this->publicHolidayDateKeys($monthStart->year);
        $dates = collect();
        $scheduledWorkingDays = 0;
        for ($date = $rangeStart->copy(); $date->lessThanOrEqualTo($rangeEnd); $date->addDay()) {
            $dateKey = $date->toDateString();
            if (! $date->isWeekday() || isset($publicHolidayDateKeys[$dateKey])) {
                continue;
            }

            if ($workingDays > 0 && $scheduledWorkingDays >= $workingDays) {
                break;
            }

            $scheduledWorkingDays++;
            if (isset($excludedDateKeys[$dateKey])) {
                continue;
            }

            $dates->push($dateKey);
        }

        return $dates;
    }

    private function mealAllowanceDailyAmount(RecurringIncome $income, ?Carbon $monthStart = null): float
    {
        $metadata = $income->metadata ?? [];
        $dailyAmount = (float) ($metadata['meal_allowance'] ?? 0);
        if ($dailyAmount > 0) {
            return round($dailyAmount, 2);
        }

        $workingDays = (int) ($metadata['working_days'] ?? 0);
        if ($workingDays <= 0) {
            $workingDays = max(1, $this->mealAllowanceScheduleDates(
                $income,
                $monthStart ?? Carbon::create($this->year, $this->month, 1),
                [],
            )->count());
        }

        return round((float) ($metadata['calculated_sa'] ?? 0) / $workingDays, 2);
    }

    private function salaryEventsForWeekdays(float $amount, Carbon $monthStart, string $label, string $subtype, string $eventKeyPrefix, ?Carbon $startsOn = null): \Illuminate\Support\Collection
    {
        $weekdays = collect();
        $monthStart = $monthStart->copy()->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        for ($date = $monthStart->copy(); $date->lessThanOrEqualTo($monthEnd); $date = $date->addDay()) {
            if ($date->isWeekday()) {
                $weekdays->push($date->toDateString());
            }
        }

        if ($startsOn) {
            $weekdays = $weekdays->filter(fn (string $date): bool => $date >= $startsOn->toDateString())->values();
        }

        if ($weekdays->isEmpty()) {
            return collect();
        }

        $dailyAmount = round($amount / self::MONTHLY_WORKING_DAYS, 2);
        if ($dailyAmount === 0.0) {
            return collect();
        }

        return $weekdays->values()->map(fn (string $date): array => [
            'event_key' => $eventKeyPrefix.':'.$date,
            'editable' => true,
            'date' => $date,
            'type' => 'salary',
            'subtype' => $subtype,
            'amount' => $dailyAmount,
            'label' => $label,
            'color' => 'text-emerald-600',
        ]);
    }

    private function employmentStartDate(?RecurringIncome $income): ?Carbon
    {
        if (! $income) {
            return null;
        }

        $startDate = $income->metadata['start_date'] ?? $income->created_at?->toDateString();

        return $startDate ? Carbon::parse($startDate)->startOfDay() : null;
    }

    public function prevMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function goToToday(): void
    {
        $today = now();
        $this->month = $today->month;
        $this->year = $today->year;
    }

    public function exportPdf()
    {
        $this->validate([
            'year' => 'required|integer|between:1,9999',
            'month' => 'required|integer|between:1,12',
        ]);

        $user = auth()->user();
        $workspace = $user->workspaces()->whereKey($user->current_workspace_id)->first();
        abort_unless($workspace, 403);
        if (in_array($workspace->type, ['business', 'company'], true)) {
            app(BusinessAccessService::class)->assert('export_reports', $user, $workspace);
        }

        $month = Carbon::create($this->year, $this->month, 1)->startOfMonth();

        $pdf = Pdf::loadView('pdf.personal-calendar', [
            'month' => $month,
            'events' => $this->dayEvents,
            'workspace' => $workspace,
            'generatedAt' => now(),
        ])
            ->setPaper('a4')
            ->setOptions(['isRemoteEnabled' => false, 'defaultFont' => 'DejaVu Sans'])
            ->output();

        return response()->streamDownload(
            static function () use ($pdf): void {
                echo $pdf;
            },
            'Calendario_'.$month->format('Y-m').'.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function openUpcomingPayment(string $dateKey): void
    {
        $date = Carbon::createFromFormat('!Y-m-d', $dateKey);
        abort_unless($date && $date->toDateString() === $dateKey, 404);
        abort_unless($this->upcomingPayments->contains('date', $dateKey), 404);

        $this->month = $date->month;
        $this->year = $date->year;
        $this->search = '';
        $this->activeFilters = [];
        unset($this->dayEvents, $this->visibleDayEvents);
        $this->openDayDetail($dateKey);
    }

    public function refreshCalendarClock(): int
    {
        return $this->nextCalendarRefreshDelay();
    }

    private function nextCalendarRefreshDelay(): int
    {
        $now = now();
        $next = $now->copy()->setTime(18, 0);
        if ($now->greaterThanOrEqualTo($next)) {
            $next = $now->copy()->addDay()->startOfDay();
        }

        return max(1, (int) ceil($now->diffInMilliseconds($next, false)));
    }

    #[Computed]
    public function dayEvents()
    {
        return $this->eventsForMonth(Carbon::create($this->year, $this->month, 1));
    }

    private function eventsForMonth(Carbon $month): Collection
    {
        $workspaceId = auth()->user()->current_workspace_id;
        $start = $month->copy()->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $settledDebtExpenseIds = Debt::where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->where('is_paid', true)
            ->whereNotNull('expense_id')
            ->pluck('expense_id');
        $expenses = Expense::where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->whereBetween('spent_at', [$start, $end])
            ->get()
            ->map(fn ($expense) => [
                'event_key' => 'expense:'.$expense->id,
                'expense_id' => $expense->id,
                'editable' => true,
                'date' => $expense->spent_at->format('Y-m-d'),
                'type' => 'expense',
                'amount' => (float) ($expense->amount_converted ?? $expense->amount),
                'bank_account_id' => $expense->bank_account_id,
                'confirmed_amount' => $settledDebtExpenseIds->contains($expense->id)
                    ? (float) ($expense->amount_converted ?? $expense->amount)
                    : ((float) $expense->amount > 0
                    ? (float) ($expense->amount_converted ?? $expense->amount)
                        * min(1, (float) ($expense->amount_paid ?: (in_array($expense->status, ['paid', 'pago'], true) ? $expense->amount : 0)) / (float) $expense->amount)
                    : 0),
                'edit_amount' => (float) $expense->amount,
                'currency' => $expense->currency,
                'label' => $expense->description ?: 'Gasto',
                'color' => 'text-red-500',
            ]);

        $incomes = Income::where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->whereBetween('received_at', [$start, $end])
            ->get()
            ->map(fn ($income) => [
                'event_key' => 'income:'.$income->id,
                'income_id' => $income->id,
                'editable' => true,
                'date' => $income->received_at->format('Y-m-d'),
                'type' => 'income',
                'amount' => (float) ($income->amount_converted ?? $income->amount),
                'bank_account_id' => $income->bank_account_id,
                'edit_amount' => (float) $income->amount,
                'currency' => $income->currency,
                'label' => $income->description ?: 'Receita',
                'color' => 'text-emerald-500',
            ]);

        $salaryPayments = SalaryPayment::with('recurringIncome')
            ->where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$start->toDateString(), $end->toDateString()])
            ->get();
        $salaryPaymentEvents = $salaryPayments->flatMap(function (SalaryPayment $payment) use ($start) {
            $metadata = $payment->recurringIncome?->metadata ?? [];
            $mealCardAllowance = ($metadata['meal_allowance_destination'] ?? null) === 'meal_card'
                ? (float) $payment->meal_allowance
                : 0;
            $salaryAmount = round(max(0, (float) $payment->net_received - $mealCardAllowance), 2);
            $label = $payment->recurringIncome?->description
                ? 'Salário recebido (valor diário) · '.$payment->recurringIncome->description
                : 'Salário recebido (valor diário)';

            return $this->salaryEventsForWeekdays(
                $salaryAmount,
                $start,
                $label,
                'salary_payment',
                'salary_payment:'.$payment->id,
                $this->employmentStartDate($payment->recurringIncome),
            )->map(fn (array $event): array => array_merge($event, [
                'salary_payment_paid_at' => $payment->paid_at->toDateString(),
                'salary_payment_id' => $payment->id,
                'salary_payment_total' => $salaryAmount,
                'salary_daily_base_amount' => $event['amount'],
            ]));
        });
        $paidSalaryContractIds = $salaryPayments->pluck('recurring_income_id');
        $monthSalaryPayments = SalaryPayment::where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->where('payment_year', $start->year)
            ->where('payment_month', $start->month)
            ->get()
            ->keyBy('recurring_income_id');

        $recurringIncomeRecords = RecurringIncome::where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->get()
            ->reject(fn (RecurringIncome $income) => ($income->metadata['income_type'] ?? null) === 'meal_card_allowance')
            ->filter(fn (RecurringIncome $income) => ($income->metadata['contract_status'] ?? 'active') === 'active')
            ->values();

        $recurringIncomes = $recurringIncomeRecords
            ->reject(fn (RecurringIncome $income) => $paidSalaryContractIds->contains($income->id))
            ->flatMap(function ($income) use ($start) {
                $day = max(1, min($start->daysInMonth, (int) ($income->day_of_month ?: 1)));
                $metadata = $income->metadata ?? [];
                $amount = (float) $income->amount;

                if (
                    ($metadata['meal_allowance_destination'] ?? null) === 'meal_card'
                    && empty($metadata['meal_card_income_id'])
                ) {
                    $amount = max(0, $amount - (float) ($metadata['calculated_sa'] ?? 0));
                }

                if ($income->source === 'emprego') {
                    return $this->salaryEventsForWeekdays(
                        $amount,
                        $start,
                        'Salário diário estimado · '.($income->description ?: 'Salário'),
                        'salary_projection',
                        'salary_projection:'.$income->id,
                        $this->employmentStartDate($income),
                    );
                }

                return collect([[
                    'event_key' => 'recurring_income:'.$income->id.':'.$start->format('Y-m'),
                    'editable' => true,
                    'income_id' => $income->id,
                    'date' => $start->copy()->day($day)->format('Y-m-d'),
                    'type' => 'salary',
                    'subtype' => 'recurring_income',
                    'amount' => $amount,
                    'bank_account_id' => $income->bank_account_id,
                    'edit_amount' => $amount,
                    'label' => $income->description ?: 'Salário',
                    'color' => 'text-emerald-600',
                ]]);
            });

        $paydayEvents = $recurringIncomeRecords
            ->filter(fn (RecurringIncome $income): bool => $income->source === 'emprego')
            ->flatMap(function (RecurringIncome $income) use ($start, $monthSalaryPayments): \Illuminate\Support\Collection {
                $paymentDay = max(1, min($start->daysInMonth, (int) ($income->day_of_month ?: 1)));
                $paymentDate = $start->copy()->day($paymentDay);
                $metadata = $income->metadata ?? [];
                $payment = $monthSalaryPayments->get($income->id);
                $mealAllowance = round(max(0, (float) ($metadata['calculated_sa']
                    ?? ((float) ($metadata['meal_allowance'] ?? 0) * (int) ($metadata['working_days'] ?? 0)))), 2);
                $mealAlreadySeparated = ($metadata['meal_allowance_destination'] ?? null) === 'meal_card'
                    && ! empty($metadata['meal_card_income_id']);
                $netSalary = round(max(0, (float) $income->amount - ($mealAlreadySeparated ? 0 : $mealAllowance)), 2);

                return collect([[
                    'event_key' => 'payday:'.$income->id.':'.$start->format('Y-m'),
                    'editable' => false,
                    'date' => $paymentDate->toDateString(),
                    'type' => 'salary',
                    'subtype' => 'payday',
                    'label' => 'Dia habitual de pagamento · '.($income->description ?: 'Salário'),
                    'payday_net_salary' => $netSalary,
                    'payday_meal_allowance' => $mealAllowance,
                    'payday_deposit_amount' => $payment
                        ? round(max(0, (float) $payment->net_received - (($metadata['meal_allowance_destination'] ?? null) === 'meal_card' ? (float) $payment->meal_allowance : 0)), 2)
                        : round($netSalary + (($metadata['meal_allowance_destination'] ?? null) === 'meal_card' ? 0 : $mealAllowance), 2),
                    'payday_paid' => $payment && $payment->status === 'paid' && $payment->paid_at && $payment->paid_at->toDateString() <= now()->toDateString(),
                    'payday_in_balance' => $payment && (float) $payment->bank_balance_amount > 0,
                    'bank_account_id' => $payment?->bank_account_id ?? $income->bank_account_id,
                    'color' => 'text-emerald-600',
                ]]);
            });

        $mealAllowanceContracts = $recurringIncomeRecords
            ->filter(fn (RecurringIncome $income) => ($income->metadata['meal_allowance_destination'] ?? null) === 'meal_card')
            ->values();
        $vacationPeriods = $this->vacationPeriodsForMonth($start);
        $vacationDateKeys = $this->vacationDateKeys($start, $vacationPeriods);
        $mealAllowanceContractIds = $mealAllowanceContracts->modelKeys();
        $dailyAllowanceRecords = $mealAllowanceContractIds === []
            ? collect()
            : DailyMealAllowance::where('workspace_id', $workspaceId)
                ->where('user_id', auth()->id())
                ->whereIn('recurring_income_id', $mealAllowanceContractIds)
                ->whereBetween('allowance_date', [$start->toDateString(), $end->toDateString()])
                ->get()
                ->keyBy(fn (DailyMealAllowance $record) => $record->recurring_income_id.':'.$record->allowance_date->format('Y-m-d'));
        $dailyMealAllowanceEvents = $mealAllowanceContracts->flatMap(function (RecurringIncome $income) use ($start, $dailyAllowanceRecords, $vacationDateKeys) {
            $scheduledDates = $this->mealAllowanceScheduleDates($income, $start, $vacationDateKeys);
            $expectedAmount = $this->mealAllowanceDailyAmount($income, $start);

            if ($expectedAmount <= 0) {
                return collect();
            }

            return $scheduledDates->map(function (string $date) use ($income, $dailyAllowanceRecords, $expectedAmount): array {
                $record = $dailyAllowanceRecords->get($income->id.':'.$date);
                $dayExpectedAmount = $record ? (float) $record->expected_amount : $expectedAmount;
                $receivedAmount = $record ? (float) $record->received_amount : $dayExpectedAmount;
                $isPastCutoff = $date < now()->toDateString()
                    || ($date === now()->toDateString() && now()->format('H:i:s') >= '18:00:00');
                $status = $record
                    ? ($receivedAmount <= 0
                        ? 'missed'
                        : ($receivedAmount < $dayExpectedAmount ? 'partial' : 'received'))
                    : ($isPastCutoff ? 'received' : 'expected');

                return [
                    'event_key' => 'meal_allowance:'.$income->id.':'.$date,
                    'editable' => false,
                    'date' => $date,
                    'type' => 'salary',
                    'subtype' => 'meal_allowance',
                    'contract_id' => $income->id,
                    'amount' => $receivedAmount,
                    'expected_amount' => $dayExpectedAmount,
                    'status' => $status,
                    'confirmed' => $record !== null,
                    'notes' => $record?->notes,
                    'label' => match ($status) {
                        'missed' => 'Subsídio não recebido',
                        'partial' => 'Subsídio parcial',
                        default => 'Subsídio de alimentação',
                    },
                    'color' => $status === 'missed'
                        ? 'text-red-500'
                        : ($status === 'partial' ? 'text-amber-500' : 'text-emerald-600'),
                ];
            });
        });

        $workMilestones = RecurringIncome::where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->where('source', 'emprego')
            ->get()
            ->reject(fn (RecurringIncome $contract) => ($contract->metadata['income_type'] ?? null) === 'meal_card_allowance')
            ->flatMap(function (RecurringIncome $contract) use ($start, $end) {
                $metadata = $contract->metadata ?? [];
                $milestones = collect();

                foreach ([
                    ['date_key' => 'start_date', 'label' => 'Primeiro dia de trabalho'],
                    ['date_key' => 'end_date', 'label' => 'Último dia de trabalho'],
                ] as $milestone) {
                    if (empty($metadata[$milestone['date_key']])) {
                        continue;
                    }

                    $date = Carbon::parse($metadata[$milestone['date_key']])->startOfDay();
                    if ($date->lessThan($start) || $date->greaterThan($end)) {
                        continue;
                    }

                    $milestones->push([
                        'event_key' => 'work_milestone:'.$contract->id.':'.$milestone['date_key'],
                        'editable' => true,
                        'contract_id' => $contract->id,
                        'date' => $date->toDateString(),
                        'type' => 'reminder',
                        'subtype' => 'work_milestone',
                        'label' => $milestone['label'].' · '.($contract->description ?: 'Contrato de trabalho'),
                        'color' => 'text-indigo-500',
                    ]);
                }

                return $milestones;
            })
            ->unique(fn (array $event): string => $event['date'].':'.$event['subtype'].':'.$event['label']);

        $vacationEvents = $vacationPeriods->flatMap(function (CalendarVacation $period) use ($start, $end) {
            $periodStart = $period->start_date->greaterThan($start) ? $period->start_date->copy() : $start->copy();
            $periodEnd = $period->end_date->lessThan($end) ? $period->end_date->copy() : $end->copy();
            $events = collect();

            for ($date = $periodStart; $date->lessThanOrEqualTo($periodEnd); $date = $date->addDay()) {
                $events->push([
                    'event_key' => 'vacation:'.$period->id.':'.$date->toDateString(),
                    'editable' => true,
                    'date' => $date->toDateString(),
                    'type' => 'reminder',
                    'subtype' => 'vacation',
                    'vacation_id' => $period->id,
                    'vacation_start_date' => $period->start_date->toDateString(),
                    'vacation_end_date' => $period->end_date->toDateString(),
                    'notes' => $period->notes,
                    'label' => 'Férias',
                    'meta' => $period->start_date->format('d/m').'–'.$period->end_date->format('d/m'),
                    'color' => 'text-emerald-600',
                ]);
            }

            return $events;
        });
        $publicHolidayEvents = $this->publicHolidayEvents($start, $end);

        $subscriptionPayments = SubscriptionPayment::where('workspace_id', $workspaceId)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($query) => $query->where('user_id', auth()->id()))
            ->whereNotNull('paid_at')
            ->whereDate('paid_at', '<=', now()->toDateString())
            ->whereBetween('billing_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy(fn (SubscriptionPayment $payment): string => $payment->subscription_id.':'.$payment->billing_date->toDateString());
        $subscriptions = Subscription::where('workspace_id', $workspaceId)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($query) => $query->where('user_id', auth()->id()))
            ->where('is_active', true)
            ->where('paid_by_company', false)
            ->get()
            ->filter(fn (Subscription $subscription): bool => $this->subscriptionDueInMonth($subscription, $start) !== false)
            ->map(function ($subscription) use ($start, $subscriptionPayments) {
                $date = $this->subscriptionDateForMonth($subscription, $start)->toDateString();

                return [
                    'event_key' => 'subscription:'.$subscription->id,
                    'subscription_id' => $subscription->id,
                    'editable' => (int) $subscription->user_id === (int) auth()->id(),
                    'date' => $date,
                    'type' => 'subscription',
                    'schedule_known' => $this->subscriptionDueInMonth($subscription, $start) !== null,
                    'amount' => (float) $subscription->amount,
                    'confirmed_amount' => min((float) $subscription->amount, (float) $subscriptionPayments->get($subscription->id.':'.$date, collect())->sum('amount')),
                    'edit_amount' => (float) $subscription->amount,
                    'label' => $subscription->name ?: 'Assinatura',
                    'color' => 'text-purple-500',
                ];
            });

        $transfers = BankTransfer::with(['fromAccount', 'toAccount'])
            ->where('workspace_id', $workspaceId)
            ->when(
                auth()->user()->currentWorkspace?->type === 'personal',
                fn ($query) => $query->where('user_id', auth()->id()),
            )
            ->whereBetween('transferred_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->get()
            ->map(function (BankTransfer $transfer): array {
                $direction = ($transfer->direction ?? 'out') === 'in' ? 'in' : 'out';
                $counterparty = $transfer->recipient
                    ?: ($direction === 'in' ? $transfer->fromAccount?->name : $transfer->toAccount?->name);

                return [
                    'event_key' => 'transfer:'.$transfer->id,
                    'transfer_id' => $transfer->id,
                    'editable' => true,
                    'date' => $transfer->transferred_at->format('Y-m-d'),
                    'type' => 'transfer',
                    'direction' => $direction,
                    'amount' => (float) $transfer->amount,
                    'currency' => $transfer->currency,
                    'label' => $transfer->description ?: ($direction === 'in' ? 'Transferência recebida' : 'Transferência enviada'),
                    'meta' => $counterparty
                        ? ($direction === 'in' ? 'De ' : 'Para ').$counterparty
                        : null,
                    'color' => 'text-blue-500',
                ];
            });

        $debts = Debt::where('workspace_id', $workspaceId)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($q) => $q->where('user_id', auth()->id()))
            ->where('is_paid', false)
            ->whereBetween('due_at', [$start, $end])
            ->get()
            ->map(fn ($debt) => [
                'event_key' => 'debt:'.$debt->id,
                'debt_id' => $debt->id,
                'editable' => (int) $debt->user_id === (int) auth()->id(),
                'date' => $debt->due_at->format('Y-m-d'),
                'type' => 'debt',
                'amount' => (float) $debt->amount,
                'debt_direction' => $debt->type,
                'bank_account_id' => $debt->bank_account_id,
                'label' => $debt->description ?: ($debt->person_name ?: 'Parcela'),
                'color' => 'text-amber-500',
            ]);

        $fitness = FitnessActivity::where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->whereBetween('activity_date', [$start, $end])
            ->get()
            ->map(fn ($fitness) => [
                'event_key' => 'fitness:'.$fitness->id,
                'fitness_id' => $fitness->id,
                'editable' => true,
                'date' => $fitness->activity_date->format('Y-m-d'),
                'type' => 'fitness',
                'label' => $fitness->type,
                'meta' => $fitness->duration_minutes.'m',
                'duration_minutes' => $fitness->duration_minutes,
                'color' => 'text-orange-500',
            ]);

        $reminders = Reminder::where('workspace_id', $workspaceId)->where('user_id', auth()->id())
            ->whereBetween('remind_at', [$start, $end])
            ->get()
            ->map(fn ($reminder) => [
                'event_key' => 'reminder:'.$reminder->id,
                'reminder_id' => $reminder->id,
                'editable' => true,
                'date' => $reminder->remind_at->format('Y-m-d'),
                'type' => 'reminder',
                'label' => $reminder->title,
                'done' => $reminder->is_completed,
                'color' => 'text-indigo-500',
            ]);

        $allEvents = collect()
            ->concat($expenses)
            ->concat($incomes)
            ->concat($salaryPaymentEvents)
            ->concat($recurringIncomes)
            ->concat($paydayEvents)
            ->concat($dailyMealAllowanceEvents)
            ->concat($subscriptions)
            ->concat($transfers)
            ->concat($debts)
            ->concat($fitness)
            ->concat($reminders)
            ->concat($workMilestones)
            ->concat($vacationEvents)
            ->concat($publicHolidayEvents);

        $eventOverrides = CalendarEventOverride::where('workspace_id', $workspaceId)
            ->where('user_id', auth()->id())
            ->get()
            ->keyBy('event_key');
        $sourceEventKeys = $allEvents->pluck('event_key')->filter()->flip();
        $allEvents = $allEvents->map(function (array $event) use ($eventOverrides): array {
            $override = $eventOverrides->get($event['event_key'] ?? '');

            return $override
                ? array_replace($event, $override->event_data, ['event_key' => $event['event_key']])
                : $event;
        });
        $overrideEvents = $eventOverrides
            ->reject(fn (CalendarEventOverride $override, string $key): bool => $sourceEventKeys->has($key))
            ->map(fn (CalendarEventOverride $override): array => array_replace(
                $override->event_data,
                [
                    'event_key' => $override->event_key,
                    'date' => $override->event_date->toDateString(),
                ],
            ));

        return $allEvents
            ->concat($overrideEvents)
            ->filter(fn (array $event): bool => $event['date'] >= $start->toDateString() && $event['date'] <= $end->toDateString())
            ->map(fn (array $event): array => array_merge($event, $this->calendarEventIndicator($event)))
            ->groupBy('date');
    }

    private function subscriptionDueInMonth(Subscription $subscription, Carbon $month): ?bool
    {
        $interval = match ($subscription->cycle) {
            'quarterly' => 3,
            'semiannual' => 6,
            'annual', 'yearly' => 12,
            'monthly', null, '' => 1,
            default => null,
        };
        $anchor = $subscription->renewal_date ?? $subscription->started_at;
        $date = $this->subscriptionDateForMonth($subscription, $month);
        if ($subscription->started_at && $date->lessThan($subscription->started_at)) {
            return false;
        }
        if ($subscription->renewal_date && $date->toDateString() >= now()->toDateString() && $date->lessThan($subscription->renewal_date)) {
            return false;
        }
        if ($interval === 1) {
            return true;
        }
        if ($interval === null || ! $anchor) {
            return null;
        }

        $months = ($month->year - $anchor->year) * 12 + $month->month - $anchor->month;

        return $months % $interval === 0;
    }

    private function subscriptionDateForMonth(Subscription $subscription, Carbon $month): Carbon
    {
        $day = max(1, min($month->daysInMonth, (int) ($subscription->renewal_date?->day ?? ($subscription->billing_day ?: 1))));

        return $month->copy()->day($day)->startOfDay();
    }

    /**
     * Versão filtrada de dayEvents(), usada só para o que é mostrado nas células/modal.
     * Não deve ser usada nos cálculos financeiros (saldo, dias de risco), que têm de
     * refletir sempre a realidade completa, independentemente dos filtros visuais ativos.
     */
    #[Computed]
    public function visibleDayEvents()
    {
        $search = trim($this->search);
        if (empty($this->activeFilters) && $search === '') {
            return $this->dayEvents;
        }

        $allowedTypes = collect($this->activeFilters)
            ->flatMap(fn ($key) => self::FILTER_TYPE_MAP[$key] ?? [])
            ->all();

        return $this->dayEvents
            ->map(fn ($events) => $events->filter(fn (array $event): bool =>
                (empty($this->activeFilters) || in_array($event['type'], $allowedTypes, true))
                && ($search === ''
                    || mb_stripos($event['label'], $search) !== false
                    || mb_stripos($event['meta'] ?? '', $search) !== false)))
            ->filter(fn ($events) => $events->isNotEmpty());
    }

    #[Computed]
    public function expenseComparison(): array
    {
        $previousMonth = Carbon::create($this->year, $this->month, 1)->subMonth();
        $previous = (float) Expense::where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->whereBetween('spent_at', [$previousMonth->copy()->startOfMonth(), $previousMonth->copy()->endOfMonth()])
            ->get(['amount', 'amount_converted'])
            ->sum(fn (Expense $expense): float => (float) ($expense->amount_converted ?? $expense->amount));
        $current = $this->dayEvents->flatten(1)->where('type', 'expense')->sum('amount');

        return [
            'previous' => round($previous, 2),
            'difference' => round($current - $previous, 2),
        ];
    }

    #[Computed]
    public function selectedDayEvents()
    {
        if (! $this->selectedDayKey) {
            return collect();
        }

        return $this->visibleDayEvents->get($this->selectedDayKey, collect());
    }

    #[Computed]
    public function selectedDaySummary(): array
    {
        $events = $this->selectedDayEvents;
        $income = (float) $events
            ->whereIn('type', ['income', 'salary'])
            ->sum(fn ($event) => (float) ($event['amount'] ?? 0));
        $outflow = (float) $events
            ->whereIn('type', ['expense', 'subscription', 'debt'])
            ->sum(fn ($event) => (float) ($event['amount'] ?? 0));
        $hasFinancialRecords = $events->contains(
            fn ($event): bool => isset($event['amount'])
                && in_array($event['type'], ['income', 'salary', 'expense', 'subscription', 'debt'], true),
        );

        return [
            'income' => $income,
            'outflow' => $outflow,
            'total' => round($income - $outflow, 2),
            'has_financial_records' => $hasFinancialRecords,
        ];
    }

    #[Computed]
    public function upcomingPayments(): Collection
    {
        $today = Carbon::parse(now()->toDateString())->startOfDay();
        $end = $today->copy()->addDays(14);
        $months = [$today->copy()->startOfMonth()];
        if (! $today->isSameMonth($end)) {
            $months[] = $end->copy()->startOfMonth();
        }

        return collect($months)->flatMap(fn (Carbon $month) => $this->eventsForMonth($month)->flatten(1))
            ->filter(fn (array $event): bool => in_array($event['type'], ['expense', 'subscription'], true)
                && ($event['schedule_known'] ?? true)
                && $event['date'] >= $today->toDateString()
                && $event['date'] <= $end->toDateString()
                && (float) $event['amount'] > (float) ($event['confirmed_amount'] ?? 0))
            ->map(fn (array $event): array => array_merge($event, [
                'outstanding_amount' => round((float) $event['amount'] - (float) ($event['confirmed_amount'] ?? 0), 2),
            ]))
            ->sortBy('date')
            ->values();
    }

    #[Computed]
    public function paymentSummary(): array
    {
        $month = Carbon::create($this->year, $this->month, 1);
        $events = $this->dayEvents->flatten(1);
        $today = now()->toDateString();
        $paidSalary = SalaryPayment::with('recurringIncome')
            ->where('workspace_id', auth()->user()->current_workspace_id)
            ->where('user_id', auth()->id())
            ->where('payment_year', $this->year)
            ->where('payment_month', $this->month)
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->whereDate('paid_at', '<=', $today)
            ->get()
            ->sum(function (SalaryPayment $payment): float {
                $mealCard = ($payment->recurringIncome?->metadata['meal_allowance_destination'] ?? null) === 'meal_card';

                return max(0, (float) $payment->net_received - ($mealCard ? (float) $payment->meal_allowance : 0));
            });
        $confirmedIncome = $events->where('type', 'income')
            ->filter(fn (array $event): bool => $event['date'] <= $today)
            ->sum('amount') + $paidSalary;
        $confirmedIncome += $events->where('subtype', 'meal_allowance')
            ->where('confirmed', true)
            ->sum('amount');
        $pendingIncome = $events->filter(fn (array $event): bool =>
            (($event['type'] === 'income') && $event['date'] > $today)
                || ($event['subtype'] ?? null) === 'recurring_income')
            ->sum('amount');
        $pendingIncome += $events->where('subtype', 'payday')
            ->filter(fn (array $event): bool => ! $event['payday_paid'])
            ->sum('payday_deposit_amount');
        $pendingIncome += $events->where('subtype', 'meal_allowance')
            ->sum(fn (array $event): float => max(0, (float) $event['expected_amount']
                - (($event['confirmed'] ?? false) ? (float) $event['amount'] : 0)));
        $pendingIncome += $events->where('type', 'debt')
            ->where('debt_direction', 'owed')
            ->sum('amount');
        $outflows = $events->whereIn('type', ['expense', 'subscription']);
        $confirmedSubscriptions = SubscriptionPayment::where('workspace_id', auth()->user()->current_workspace_id)
            ->when(auth()->user()->currentWorkspace?->type === 'personal', fn ($query) => $query->where('user_id', auth()->id()))
            ->whereNotNull('paid_at')
            ->whereDate('paid_at', '<=', $today)
            ->whereBetween('billing_date', [$month->copy()->startOfMonth()->toDateString(), $month->copy()->endOfMonth()->toDateString()])
            ->sum('amount');
        $confirmedOutflow = $outflows->where('type', 'expense')->sum(fn (array $event): float => (float) ($event['confirmed_amount'] ?? 0))
            + (float) $confirmedSubscriptions;
        $pendingOutflow = $outflows->filter(fn (array $event): bool => $event['schedule_known'] ?? true)
            ->sum(fn (array $event): float => max(0, (float) $event['amount'] - (float) ($event['confirmed_amount'] ?? 0)));
        $pendingOutflow += $events->where('type', 'debt')
            ->where('debt_direction', 'owe')
            ->sum('amount');

        return [
            'received' => round((float) $confirmedIncome, 2),
            'receivable' => round((float) $pendingIncome, 2),
            'paid' => round((float) $confirmedOutflow, 2),
            'payable' => round((float) $pendingOutflow, 2),
        ];
    }

    #[Computed]
    public function currentAccountBalance(): array
    {
        $user = auth()->user();
        $workspaceId = $user->current_workspace_id;
        $accounts = BankAccount::where('workspace_id', $workspaceId)
            ->when($user->currentWorkspace?->type === 'personal', fn ($query) => $query->where('user_id', $user->id))
            ->where('include_in_total', true)
            ->get(['id', 'balance', 'currency']);
        if ($accounts->isEmpty()) {
            return ['available' => false, 'reason' => 'no_accounts', 'balance' => null, 'projection_balance' => null, 'account_ids' => []];
        }
        $workspaceCurrency = strtoupper((string) ($user->currentWorkspace?->currency ?: 'EUR'));
        if ($accounts->contains(fn (BankAccount $account): bool => strtoupper((string) $account->currency) !== $workspaceCurrency)) {
            return ['available' => false, 'reason' => 'currency', 'balance' => null, 'projection_balance' => null, 'account_ids' => []];
        }

        $accountIds = $accounts->modelKeys();
        $balance = (float) $accounts->sum('balance');
        $projectionBalance = $balance;
        foreach ([
            [Income::class, 'received_at', 1],
            [Expense::class, 'spent_at', -1],
        ] as [$model, $dateColumn, $sign]) {
            $records = $model::where('workspace_id', $workspaceId)
                ->when($user->currentWorkspace?->type === 'personal', fn ($query) => $query->where('user_id', $user->id))
                ->whereIn('bank_account_id', $accountIds)
                ->get(['amount', 'amount_converted', $dateColumn]);
            $recordAmount = fn ($record): float => (float) ($record->amount_converted ?? $record->amount);
            $balance += $sign * (float) $records
                ->filter(fn ($record): bool => $record->{$dateColumn}->toDateString() <= now()->toDateString())
                ->sum($recordAmount);
            $projectionBalance += $sign * (float) $records->sum($recordAmount);
        }

        return ['available' => true, 'reason' => null, 'balance' => $balance, 'projection_balance' => $projectionBalance, 'account_ids' => $accountIds];
    }

    #[Computed]
    public function cashProjection(): array
    {
        $currentBalance = $this->currentAccountBalance;
        if ($currentBalance['reason'] === 'no_accounts') {
            return ['available' => false, 'reason' => $currentBalance['reason'], 'risk_days' => collect(), 'final_balance' => null];
        }

        $targetMonth = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $today = Carbon::parse(now()->toDateString())->startOfDay();
        if ($targetMonth->copy()->endOfMonth()->lessThan($today)) {
            return ['available' => false, 'reason' => 'past', 'risk_days' => collect(), 'final_balance' => null];
        }
        if (! $currentBalance['available']) {
            return ['available' => false, 'reason' => $currentBalance['reason'], 'risk_days' => collect(), 'final_balance' => null];
        }

        $accountIds = $currentBalance['account_ids'];
        $balance = $currentBalance['projection_balance'];
        $riskDays = collect();
        for ($month = $today->copy()->startOfMonth(); $month->lessThanOrEqualTo($targetMonth); $month->addMonthNoOverflow()) {
            $eventsByDate = $month->isSameMonth($targetMonth) ? $this->dayEvents : $this->eventsForMonth($month);
            $start = $month->isSameMonth($today) ? $today->copy() : $month->copy();
            $end = $month->copy()->endOfMonth()->startOfDay();
            for ($date = $start; $date->lessThanOrEqualTo($end); $date->addDay()) {
                foreach ($eventsByDate->get($date->toDateString(), collect()) as $event) {
                    $linkedAccount = $event['bank_account_id'] ?? null;
                    if ($event['type'] === 'expense' && $linkedAccount === null) {
                        $balance -= max(0, (float) $event['amount'] - (float) ($event['confirmed_amount'] ?? 0));
                    } elseif ($event['type'] === 'subscription' && ($event['schedule_known'] ?? true)) {
                        $balance -= max(0, (float) $event['amount'] - (float) ($event['confirmed_amount'] ?? 0));
                    } elseif ($event['type'] === 'debt' && in_array($linkedAccount, $accountIds) && in_array($event['debt_direction'] ?? null, ['owe', 'owed'], true)) {
                        $balance += (($event['debt_direction'] ?? null) === 'owed' ? 1 : -1) * (float) $event['amount'];
                    } elseif (($event['subtype'] ?? null) === 'payday' && ! $event['payday_paid'] && ! $event['payday_in_balance'] && in_array($linkedAccount, $accountIds)) {
                        $balance += (float) $event['payday_deposit_amount'];
                    } elseif ($event['type'] === 'income' && $event['date'] > $today->toDateString() && $linkedAccount === null) {
                        $balance += (float) $event['amount'];
                    } elseif (($event['subtype'] ?? null) === 'recurring_income' && in_array($linkedAccount, $accountIds)) {
                        $balance += (float) $event['amount'];
                    }
                }

                if ($date->greaterThanOrEqualTo($targetMonth) && $balance < 0) {
                    $riskDays->put($date->toDateString(), [
                        'date' => $date->copy(),
                        'projected_balance' => round($balance, 2),
                    ]);
                }
            }
        }

        return ['available' => true, 'reason' => null, 'risk_days' => $riskDays, 'final_balance' => round($balance, 2)];
    }

    #[Computed]
    public function cashRiskDays(): Collection
    {
        return $this->cashProjection['risk_days'];
    }

    #[Computed]
    public function monthSummary(): array
    {
        $now = now();
        $today = $now->toDateString();
        $includeTodayIncome = $now->format('H:i:s') >= '18:00:00';
        $events = $this->dayEvents->flatten(1)
            ->filter(fn (array $event): bool => $event['date'] <= $today);

        $income = round((float) $events
            ->whereIn('type', ['income', 'salary'])
            ->filter(fn (array $event): bool => $event['date'] < $today || $includeTodayIncome)
            ->sum(fn ($event) => (float) ($event['amount'] ?? 0)), 2);

        $outflow = round((float) $events
            ->whereIn('type', ['expense', 'subscription', 'debt'])
            ->sum(fn ($event) => (float) ($event['amount'] ?? 0)), 2);

        return [
            'income' => $income,
            'outflow' => $outflow,
            'net_total' => round($income - $outflow, 2),
            'current_balance' => $this->currentAccountBalance['balance'],
            'risk_available' => $this->currentAccountBalance['available'],
        ];
    }

    #[Computed]
    public function monthlyFinanceInsights(): array
    {
        $events = $this->dayEvents->flatten(1);
        $paidSalaryEvents = $events
            ->where('subtype', 'salary_payment');
        $incomeEvents = $events->filter(fn (array $event): bool =>
            $event['type'] === 'income'
                || in_array($event['subtype'] ?? null, ['salary_projection', 'meal_allowance', 'recurring_income'], true)
                || ($event['type'] === 'debt' && ($event['debt_direction'] ?? null) === 'owed'));
        $paidSalaryTotal = $paidSalaryEvents
            ->groupBy('salary_payment_id')
            ->sum(function ($paymentEvents): float {
                $firstEvent = $paymentEvents->first();
                $overrideDelta = $paymentEvents->sum(fn (array $event): float => array_key_exists('salary_daily_base_amount', $event)
                    ? (float) $event['amount'] - (float) $event['salary_daily_base_amount']
                    : 0);

                return (float) ($firstEvent['salary_payment_total'] ?? $paymentEvents->sum('amount')) + $overrideDelta;
            });
        $outflows = $events->filter(fn (array $event): bool =>
            $event['type'] === 'expense'
                || ($event['type'] === 'subscription' && ($event['schedule_known'] ?? true))
                || ($event['type'] === 'debt' && ($event['debt_direction'] ?? null) === 'owe'));
        $received = round(
            (float) $incomeEvents->sum(fn (array $event): float => (float) ($event['amount'] ?? 0)) + $paidSalaryTotal,
            2,
        );
        $spent = round((float) $outflows->sum(fn (array $event): float => (float) ($event['amount'] ?? 0)), 2);
        $largestExpense = $outflows->sortByDesc('amount')->first();

        return [
            'received' => $received,
            'spent' => $spent,
            'balance' => round($received - $spent, 2),
            'has_unknown_subscriptions' => $events->contains(fn (array $event): bool =>
                $event['type'] === 'subscription' && ! ($event['schedule_known'] ?? true)),
            'largest_expense' => $largestExpense ? [
                'label' => $largestExpense['label'],
                'amount' => (float) $largestExpense['amount'],
                'date' => $largestExpense['date'],
            ] : null,
        ];
    }

    public function render()
    {
        $currentDate = Carbon::create($this->year, $this->month, 1);
        $daysInMonth = $currentDate->daysInMonth;

        $firstDay = $currentDate->dayOfWeek;
        $padding = ($firstDay === 0) ? 6 : $firstDay - 1;

        return view('livewire.personal-calendar', [
            'totalDays' => $daysInMonth,
            'paddingDays' => $padding,
            'currentMonthName' => $currentDate->translatedFormat('F'),
            'monthSummary' => $this->monthSummary,
            'monthlyFinanceInsights' => $this->monthlyFinanceInsights,
            'riskDays' => $this->cashRiskDays,
            'calendarRefreshDelay' => $this->nextCalendarRefreshDelay(),
        ]);
    }
}
