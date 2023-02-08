<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'title'           => $this->title,
            'backgroundColor' => $this->color ?? '#000',
            'borderColor'     => $this->color ?? '#000',
            'id'              => (int) $this->id,
            'allDay'          => $this->isAllDay(),

            $this->mergeWhen(!$this->isRepeated(), [
                'start'           => $this->fullStartDate,
                'end'             => $this->fullEndDate,
            ]),

            'extendedProps'   => [
                'peoples'    => $this->peoples,
                'competency_id'    => $this->competency_id,
                'description'    => $this->description,
                $this->mergeWhen(!$this->isRepeated(), [
                    'hasEndTime'   => !is_null($this->end_time),
                ]),
                $this->mergeWhen($this->isRepeated(), [
                    'rrule'           => $this->rrule,
                    'isRepeated'   => $this->isRepeated(),
                ]),
                'note'           => $this->note,
            ],
        ];
    }
}
