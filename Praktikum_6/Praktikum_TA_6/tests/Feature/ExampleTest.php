<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // Halaman awal diarahkan ke form pelaporan yang terbuka untuk warga.
        $response->assertRedirect(route('laporan.create'));
    }
}
