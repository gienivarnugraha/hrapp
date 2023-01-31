<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    public function definition()
    {
      [$startDate, $endDate] = $this->getDates();
      return [
        'title'         => $this->faker->sentence(2),
        'color'         => $this->faker->hexColor(),
        'description'   => $this->faker->sentence(4),
        'note'          => $this->faker->sentence(4),
        'start_date'    => $startDate->format('Y-m-d'),
        'start_time'    => $startDate->format('H:i:s'),
        'end_date'      => $endDate->format('Y-m-d'),
        'end_time'      => $endDate->format('H:i:s'),
      ];
    }
  
  
    /**
     * Indicate that the activity is all day.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function allDay()
    {
      return $this->state(function (array $attributes) {
        return [
          'start_time' => null,
          'end_time' => null,
        ];
      });
    }
  
  
    /**
     * Get the dates for the activity
     *
     * @return array
     */
    protected function getDates()
    {
      $startDate = $this->faker->dateTimeBetween('-4 weeks', '+4 weeks');
      // Round to nearest 15
      $roundedStartSeconds = round($startDate->getTimestamp() / (15 * 60)) * (15 * 60);
      $startDate->setTime(date('H', rand(32400,54000)), date('i', $roundedStartSeconds), 0);
  
      $endDate = clone $startDate;
      // Add one or zero days to end date and the add 30 minutes
      $endDate->add(new \DateInterval('P' . rand(0, 1) . 'D'));
      $endDate->add(new \DateInterval('PT30M'));
  
      return [$startDate, $endDate];
    }
}
