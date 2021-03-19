<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->contenders_reward == null && $this->whenLoaded('contest_rewards')) {
            foreach ($this->whenLoaded('contest_rewards') as $contest_reward) {
                $this->contenders_reward += $contest_reward->reward;
            }
        }

        return [
            'id' => $this->id,
            'balance' => $this->balance,
            'title' => $this->title,
            'hash' => $this->hash,
            'last_paid' => $this->last_paid,
            'link' => $this->link,
            'max_voting_attempts' => $this->max_voting_attempts,
            'jury_id' => $this->jury_id,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'voting_end_at' => $this->voting_end_at,
            'contenders_reward' => $this->contenders_reward,
            'juries_reward_percent' => $this->juries_reward_percent,
            'juries_reward' => ($this->contenders_reward && $this->juries_reward_percent) ?
                ($this->contenders_reward * $this->juries_reward_percent)/100 : null,
            'contenders' => $this->whenLoaded('contenders'),
            'juries' => $this->whenLoaded('juries'),
            'submissions' => $this->whenLoaded('submissions'),
            'votes' => $this->whenLoaded('votes'),
            'contest_rewards' => $this->whenLoaded('contest_rewards'),
        ];
    }
}
