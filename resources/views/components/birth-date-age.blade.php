@props(['initialDate' => ''])

<div
    class="space-y-1.5"
    x-data="{
        birthDate: @js($initialDate ?? ''),
        get age() {
            if (!/^\d{4}-\d{2}-\d{2}$/.test(this.birthDate) || this.$refs.birthDate.validity.rangeOverflow) return null;

            const [year, month, day] = this.birthDate.split('-').map(Number);
            const birth = new Date(0);
            birth.setHours(0, 0, 0, 0);
            birth.setFullYear(year, month - 1, day);
            if (birth.getFullYear() !== year || birth.getMonth() !== month - 1 || birth.getDate() !== day) return null;

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (birth > today) return null;

            return today.getFullYear() - year - (today.getMonth() + 1 < month || (today.getMonth() + 1 === month && today.getDate() < day) ? 1 : 0);
        }
    }"
    x-init="birthDate = $refs.birthDate.value || birthDate"
    x-on:input="birthDate = $refs.birthDate.value"
    x-on:change="birthDate = $refs.birthDate.value"
>
    {{ $slot }}
    <p id="birth-date-age" aria-live="polite" class="text-xs font-semibold text-emerald-700 dark:text-emerald-400" x-text="age === null ? '' : `Idade: ${age} ${age === 1 ? 'ano' : 'anos'}`"></p>
</div>
