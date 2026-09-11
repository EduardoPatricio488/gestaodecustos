<?php

namespace Tests\Unit\AI;

use App\Services\AI\AiDecisionEngine;
use PHPUnit\Framework\TestCase;

class AiDecisionEngineTest extends TestCase
{
    public function test_high_confidence_critical_event_can_interrupt(): void
    {
        $result = (new AiDecisionEngine)->decide([
            'impact' => 98,
            'relevance' => 96,
            'urgency' => 95,
            'confidence' => 99,
            'novelty' => 90,
            'frequency' => 80,
        ]);

        $this->assertTrue($result['should_notify']);
        $this->assertTrue($result['should_interrupt']);
        $this->assertSame('critical', $result['priority']);
        $this->assertGreaterThanOrEqual(90, $result['score']);
    }

    public function test_low_value_event_is_suppressed(): void
    {
        $result = (new AiDecisionEngine)->decide([
            'impact' => 10,
            'relevance' => 20,
            'urgency' => 10,
            'confidence' => 60,
            'novelty' => 10,
            'frequency' => 20,
        ]);

        $this->assertFalse($result['should_notify']);
        $this->assertFalse($result['should_interrupt']);
        $this->assertSame('info', $result['priority']);
    }

    public function test_scores_are_clamped_to_safe_bounds(): void
    {
        $result = (new AiDecisionEngine)->decide([
            'impact' => 500,
            'relevance' => -20,
            'urgency' => 500,
            'confidence' => 500,
            'novelty' => -50,
            'frequency' => 500,
        ]);

        $this->assertLessThanOrEqual(100, $result['score']);
        $this->assertGreaterThanOrEqual(0, $result['score']);
    }
}
