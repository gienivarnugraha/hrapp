<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    
  protected $fillable = [
    'title',
    'description',
    'note',
    'start_date',
    'start_time',
    'end_date',
    'end_time',
    'color',
    'all_day',
    'competency_id'
  ];

  /**
   * Get the user that owns the Events
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function peoples(): BelongsToMany
  {
    return $this->belongsToMany(People::class);
  }

  /**
   * Get the user that owns the Events
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function competency(): BelongsTo
  {
    return $this->belongsTo(Competency::class);
  }

  /**
   * Get isDue attribute
   *
   * @return \Illuminate\Database\Eloquent\Casts\Attribute
   */
  public function isDue(): Attribute
  {
    return Attribute::get(function () {
      return $this->full_start_date < date('Y-m-d H:i:s');
    });
  }

  /**
   * Get the full start date in UTC including the time (if has)
   *
   * @return \Illuminate\Database\Eloquent\Casts\Attribute
   */
  public function fullStartDate(): Attribute
  {
    return Attribute::get(function () {
      $startDate = $this->asDateTime($this->start_date);

      return $this->start_time ?
        $startDate->format('Y-m-d') . 'T' . $this->start_time :
        $startDate->format('Y-m-d');
    });
  }


  /**
   * Get the full end date in UTC including the time (if has)
   *
   * @return \Illuminate\Database\Eloquent\Casts\Attribute
   */
  public function fullEndDate(): Attribute
  {
    return Attribute::get(function () {
      $endDate = $this->asDateTime($this->end_date);

      return $this->end_time ?
        $endDate->format('Y-m-d') . 'T' . $this->end_time :
        $endDate->format('Y-m-d');
    });
  }

  /**
   * Indicates whether the activity is all day event
   *
   * @return boolean
   */
  public function isAllDay(): bool
  {
    return is_null($this->start_time) && is_null($this->end_time);
  }

  /**
   * Indicates whether the activity is all day event
   *
   * @return boolean
   */
  public function isRepeated(): bool
  {
    return !is_null($this->rrule);
  }
}
