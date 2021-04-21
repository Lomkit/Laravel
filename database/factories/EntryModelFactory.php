<?php

namespace Lomkit\Laravel\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EntryModelFactory extends Factory
{
    /**
     * Get the name of the model that is generated by the factory.
     *
     * @return string
     */
    public function modelName()
    {
        return EntryModel::class;
    }

    /**
     * {@inheritdoc}
     */
    public function definition()
    {
        return [
            'sequence' => random_int(1, 10000),
            'uuid' => $this->faker->uuid,
            'batch_id' => $this->faker->uuid,
            'content' => [$this->faker->word => $this->faker->word],
        ];
    }
}
