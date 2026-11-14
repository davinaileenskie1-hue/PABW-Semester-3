<?php

namespace Modules\Buku\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Buku\Models\Buku;

/**
 * @extends Factory<Buku>
 */
class BukuFactory extends Factory
{
    protected $model = Buku::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'judul' => fake()->sentence(3),
            'penulis' => fake()->name(),
            'penerbit' => fake()->company(),
            'tahun_terbit' => fake()->numberBetween(2000, (int) date('Y')),
            'stok' => fake()->numberBetween(1, 25),
        ];
    }
}
