use App\Livewire\YearlyReport;
use App\Mail\VerifyAccountMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// ══════════════════════════════════════════════════════════════════
// 1. ÁREAS EXTERNAS E PÚBLICAS (Acessíveis por Visitantes)
// ══════════════════════════════════════════════════════════════════

// Alguns browsers/proxies continuam a pedir /favicon.ico diretamente.
// Servimos o mesmo favicon oficial do saco de moedas, em SVG, para evitar
// que o browser recorra ao favicon Laravel/default.
Route::get('/favicon.ico', function () {
    return response()->file(public_path('favicon-money-bag.svg'), [
        'Content-Type' => 'image/svg+xml',
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ]);
})->name('favicon');

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();

        return response()->json(['status' => 'ok'], 200, ['Cache-Control' => 'no-store']);
    } catch (Throwable $e) {
        Log::error('Health check failed', ['exception' => get_class($e)]);

        return response()->json(['status' => 'degraded'], 503, ['Cache-Control' => 'no-store']);
    }
})->middleware('throttle:public')->name('health.status');

Route::get('/robots.txt', function () {