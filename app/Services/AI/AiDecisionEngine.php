<?php

namespace App\Services\AI;

final class AiDecisionEngine
{
    public function decide(array $candidate): array
    {
        $impact = $this->score($candidate, 'impact', 50);
        $relevance = $this->score($candidate, 'relevance', 50);
        $urgency = $this->score($candidate, 'urgency', 50);
        $confidence = $this->score($candidate, 'confidence', 50);
        $novelty = $this->score($candidate, 'novelty', 50);
        $frequency = $this->score($candidate, 'frequency', 50);

        $score = (int) round(
            ($impact * 0.25) +
            ($relevance * 0.25) +
            ($urgency * 0.15) +
            ($confidence * 0.20) +
            ($novelty * 0.10) +
            ($frequency * 0.05)
        );

        $priority = match (true) {
            $score >= 90 => 'critical',
            $score >= 75 => 'high',
            $score >= 55 => 'medium',
            $score >= 35 => 'low',
            default => 'info',
        };

        return [
            'should_notify' => $score >= 55,
            'should_interrupt' => $score >= 90 && $confidence >= 90,
            'priority' => $priority,
            'score' => $score,
            'factors' => compact('impact', 'relevance', 'urgency', 'confidence', 'novelty', 'frequency'),
        ];
    }

    private function score(array $candidate, string $key, int $default): int
    {
        return max(0, min(100, (int) ($candidate[$key] ?? $default)));
    }
}
