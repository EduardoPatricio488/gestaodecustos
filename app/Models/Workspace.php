<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Workspace extends Model
{
    protected $fillable = [
        'name','type','owner_id','invite_code','legal_name','logo_path','tax_number','industry','currency','initial_capital','country_code','vat_rate','vat_regime',
        'audit_token','audit_access_code','audit_token_expires_at','audit_token_revoked_at','audit_token_purpose','recruitment_extra_info','business_email','plan','plan_expires_at','address','recruitment_active',
        'recruitment_description','recruitment_announcement','recruitment_vacancies','fiscal_year_start',
    ];
    protected $casts = ['audit_token_expires_at'=>'datetime','audit_token_revoked_at'=>'datetime','initial_capital'=>'decimal:2','vat_rate'=>'decimal:2','fiscal_year_start'=>'integer'];
    protected $attributes = ['type'=>'business','currency'=>'EUR','country_code'=>'PT','vat_rate'=>23,'vat_regime'=>'normal','fiscal_year_start'=>1];

    public function generateInviteCode()
    {
        if (! $this->invite_code) {
            $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/','',$this->name),0,3));
            $this->invite_code = $prefix.'-'.strtoupper(bin2hex(random_bytes(3)));
            $this->save();
        }
        return $this->invite_code;
    }
    public function getLogoUrlAttribute(): string
    {
        if (! $this->logo_path) return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=10b981&background=ecfdf5&bold=true';
        return Storage::url(preg_replace('#^/?storage/#','',$this->logo_path));
    }
    public function owner(): BelongsTo { return $this->belongsTo(User::class,'owner_id'); }
    public function users(): BelongsToMany { return $this->belongsToMany(User::class,'workspace_user')->withPivot('role')->withTimestamps(); }
    public function expenses(): HasMany { return $this->hasMany(Expense::class); }
    public function incomes(): HasMany { return $this->hasMany(Income::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
    public function categories(): HasMany { return $this->hasMany(Category::class); }
    public function clients(): HasMany { return $this->hasMany(Client::class); }
    public function suppliers(): HasMany { return $this->hasMany(Supplier::class); }
    public function projects(): HasMany { return $this->hasMany(Project::class); }
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function documents(): HasMany { return $this->hasMany(BusinessDocument::class); }
    public function tasks(): HasMany { return $this->hasMany(Task::class); }
    public function messages(): HasMany { return $this->hasMany(BusinessMessage::class); }
    public function proposals(): HasMany { return $this->hasMany(Proposal::class); }
    public function bankAccounts(): HasMany { return $this->hasMany(BankAccount::class); }
    public function bankAccessRequests(): HasMany { return $this->hasMany(BankAccessRequest::class); }
    public function absences(): HasMany { return $this->hasMany(Absence::class); }
    public function recruitmentJobs(): HasMany { return $this->hasMany(RecruitmentJob::class); }
    public function recurringIncomes(): HasMany { return $this->hasMany(RecurringIncome::class); }

    public function getTypeText(): string { return match ($this->type) {'personal'=>'Conta Individual','couple'=>'Conta Partilhada (Casal)','family'=>'Conta Familiar','business','company'=>'Gestão Empresarial',default=>'Outro'}; }

    public function getBurnRate(): float
    {
        $query = $this->expenses()->where('is_company',true)->where('spent_at','>=',now()->subMonths(3));
        $total = (float) $query->sum('amount');
        $months = $query->pluck('spent_at')->map(fn($date)=>Carbon::parse($date)->format('Y-m'))->unique()->count();
        $monthlyOperating = $total / max(1,$months);
        $monthlyPayroll = (float) $this->employees()->where('active',true)->where('suspended',false)->whereNull('terminated_at')->sum('salary');
        return round($monthlyOperating + $monthlyPayroll,2);
    }

    public function getLiquidezAtual(): float
    {
        if ($this->bankAccounts()->exists()) {
            return round((float) $this->bankAccounts()->where('type', '!=', 'credito')->get()->sum(fn ($account) => (float) $account->current_balance), 2);
        }
        $revenue = (float)$this->invoices()->where('status','paga')->sum('total_amount_converted');
        $spent = (float)$this->expenses()->where('is_company',true)->sum('amount_converted');
        return round((float)($this->initial_capital ?? 0) + $revenue - $spent,2);
    }

    public function money($amount): string
    {
        $symbols=['EUR'=>'€','USD'=>'$','BRL'=>'R$','GBP'=>'£','CHF'=>'CHF','JPY'=>'¥'];
        $symbol=$symbols[$this->currency] ?? $this->currency;
        return in_array($this->currency,['USD','BRL']) ? $symbol.' '.number_format($amount,2,',',' ') : number_format($amount,2,',',' ').' '.$symbol;
    }

    protected static function booted(): void
    {
        static::created(function ($workspace) {
            $defaults=[
                ['name'=>'Alimentação','icon'=>'shopping-cart','color'=>'#ef4444'],['name'=>'Carro','icon'=>'truck','color'=>'#f59e0b'],['name'=>'Casa','icon'=>'home','color'=>'#3b82f6'],['name'=>'Educação','icon'=>'academic-cap','color'=>'#6366f1'],['name'=>'Empréstimos','icon'=>'banknotes','color'=>'#10b981'],['name'=>'Entretenimento','icon'=>'film','color'=>'#a855f7'],['name'=>'Saúde','icon'=>'heart','color'=>'#f43f5e'],['name'=>'Seguros','icon'=>'shield-check','color'=>'#0ea5e9'],['name'=>'Tecnologia','icon'=>'cpu-chip','color'=>'#06b6d4'],['name'=>'Transporte','icon'=>'bolt','color'=>'#64748b'],
            ];
            foreach($defaults as $index=>$data) $workspace->categories()->create($data+['slug'=>str($data['name'])->slug(),'is_fixed'=>true,'order'=>$index,'user_id'=>$workspace->owner_id]);
        });
    }

    public function getRunway(): string
    {
        $burn=$this->getBurnRate(); $cash=$this->getLiquidezAtual();
        if($burn<=0) return '∞'; if($cash<=0) return '0 meses';
        return number_format($cash/$burn,1).' meses';
    }

    public function calculateScore(): int
    {
        $monthStart=now()->startOfMonth();
        $spent=(float)$this->expenses()->where('spent_at','>=',$monthStart)->sum('amount');
        $earned=(float)$this->incomes()->where('received_at','>=',$monthStart)->sum('amount');
        $budget=(float)$this->categories()->sum('budget_limit');
        $net=$earned-$spent; $savings=$earned>0?($net/$earned)*100:0; $adherence=$budget>0?(1-(min($spent,$budget)/$budget))*100:100;
        return (int)max(0,min(100,($savings*.7)+($adherence*.3)+20));
    }
}
