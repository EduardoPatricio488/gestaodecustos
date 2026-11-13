@props(['useNames' => false, 'selected' => null])

@foreach (config('countries') as $code => $name)
    <option value="{{ $useNames ? $name : $code }}" @selected($selected === ($useNames ? $name : $code))>{{ mb_chr(0x1F1E6 + ord($code[0]) - 65) }}{{ mb_chr(0x1F1E6 + ord($code[1]) - 65) }} {{ $name }}</option>
@endforeach
