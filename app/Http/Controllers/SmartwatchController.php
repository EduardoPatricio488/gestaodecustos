<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmartwatchController extends Controller
{
    public function info(Request $request)
    {
        $device = trim($request->input('device', ''));
        if (!$device) return response()->json(['error' => 'Nome do dispositivo em falta.'], 400);

        // Prompt detalhado para garantir a resposta em JSON
        $prompt = "És um analista técnico de wearables. Analisa o dispositivo: \"{$device}\".
Devolve EXCLUSIVAMENTE um objeto JSON puro.
Estrutura:
{
  \"name\": \"Nome\", \"brand\": \"Marca\", \"emoji\": \"⌚\", \"year\": \"2024\",
  \"battery\": \"14 dias\", \"gps\": true, \"nfc\": true,
  \"display\": \"AMOLED 1.62' 490 nits\",
  \"water\": \"5ATM (50m)\",
  \"materials\": \"Alumínio e Vidro Temperado\",
  \"health_metrics\": [\"Ritmo Cardíaco\", \"SpO2\", \"Stress\", \"Sono\", \"VO2 Max\"],
  \"sensors\": [\"Acelerómetro\", \"Giroscópio\", \"Sensor de Luz Amb.\"],
  \"sports\": [\"+150 Modos\", \"Running Courses\"],
  \"integration_tip\": \"Dica de ligação curta\",
  \"pros\": [\"Ecrã brilhante\", \"Bateria longa\"],
  \"cons\": [\"Sem GPS nativo\", \"App limitada\"],
  \"coach_advice\": \"Conselho atrevido em PT-PT.\"
}";

        try {
            $apiKey = env('OPENROUTER_API_KEY');

            // USANDO O MESMO MODELO E HEADERS QUE FUNCIONAM NO TEU AI-INSIGHTS
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
                'HTTP-Referer'  => config('app.url'),
                'X-Title'       => config('app.name'),
            ])->timeout(60)->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'openrouter/owl-alpha', // MESMO MODELO DO TEU AIINSIGHTS
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
            ]);

            if ($response->successful()) {
                $content = $response->json()['choices'][0]['message']['content'] ?? '';

                // Limpeza de Markdown (extrai apenas o conteúdo entre { })
                if (preg_match('/\{.*\}/s', $content, $matches)) {
                    $content = $matches[0];
                }

                $data = json_decode(trim($content), true);

                if (!$data) {
                    Log::error("IA Response Inválida: " . $content);
                    return response()->json(['error' => 'A IA não enviou dados num formato legível.'], 500);
                }

                return response()->json($data);
            } else {
                Log::error("OpenRouter Error: " . $response->body());
                return response()->json(['error' => 'Erro ' . $response->status() . ' do OpenRouter.'], 500);
            }
        } catch (\Throwable $e) {
            Log::error("Falha Smartwatch IA: " . $e->getMessage());
            return response()->json(['error' => 'Falha técnica na ligação.'], 500);
        }
    }
}
