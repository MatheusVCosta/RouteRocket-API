<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RedirectLog>
 */
class RedirectLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'redirect_id'        => $this->faker->uuid(),
            'ip_address_request' => $this->faker->ipv4(),
            'user_agent'         => $this->faker->userAgent(),
            'header_referer'     => $this->faker->url(),
            'last_access_at'     => $this->faker->dateTimeThisYear(),
        ];
    }
}
