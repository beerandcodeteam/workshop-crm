<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeadService
{
    public function createWithDeal(
        User $owner,
        string $leadName,
        string $leadEmail,
        ?string $leadPhone,
        string $dealTitle,
        string $dealValue,
    ): Deal {
        return DB::transaction(function () use ($owner, $leadName, $leadEmail, $leadPhone, $dealTitle, $dealValue) {
            $lead = Lead::create([
                'user_id' => $owner->id,
                'name' => $leadName,
                'email' => $leadEmail,
                'phone' => $leadPhone,
            ]);

            return $this->createDealForLead($lead, $owner, $dealTitle, $dealValue);
        });
    }

    public function createDealForExistingLead(
        Lead $lead,
        User $owner,
        string $dealTitle,
        string $dealValue,
    ): Deal {
        return $this->createDealForLead($lead, $owner, $dealTitle, $dealValue);
    }

    private function createDealForLead(Lead $lead, User $owner, string $dealTitle, string $dealValue): Deal
    {
        $newLeadStage = PipelineStage::where('name', 'New Lead')->first();

        return Deal::create([
            'lead_id' => $lead->id,
            'user_id' => $owner->id,
            'pipeline_stage_id' => $newLeadStage->id,
            'title' => $dealTitle,
            'value' => $dealValue,
            'sort_order' => 0,
        ]);
    }
}
