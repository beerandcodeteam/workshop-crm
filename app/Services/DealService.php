<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\PipelineStage;
use Illuminate\Support\Facades\DB;

class DealService
{
    public function moveToStage(Deal $deal, int $stageId, int $position): void
    {
        DB::transaction(function () use ($deal, $stageId, $position) {
            $oldStageId = $deal->pipeline_stage_id;

            $deal->update([
                'pipeline_stage_id' => $stageId,
                'sort_order' => $position,
            ]);

            $this->recalculateSortOrder($stageId, $deal->id, $position);

            if ($oldStageId !== $stageId) {
                $this->recalculateSortOrderAfterRemoval($oldStageId);
            }
        });
    }

    private function recalculateSortOrder(int $stageId, int $movedDealId, int $targetPosition): void
    {
        $deals = Deal::where('pipeline_stage_id', $stageId)
            ->where('id', '!=', $movedDealId)
            ->orderBy('sort_order')
            ->get();

        $position = 0;
        foreach ($deals as $deal) {
            if ($position === $targetPosition) {
                $position++;
            }
            $deal->update(['sort_order' => $position]);
            $position++;
        }
    }

    private function recalculateSortOrderAfterRemoval(int $stageId): void
    {
        $deals = Deal::where('pipeline_stage_id', $stageId)
            ->orderBy('sort_order')
            ->get();

        foreach ($deals as $index => $deal) {
            if ($deal->sort_order !== $index) {
                $deal->update(['sort_order' => $index]);
            }
        }
    }

    public function requiresLossReason(int $stageId): bool
    {
        return PipelineStage::where('id', $stageId)->where('name', 'Lost')->exists();
    }
}
