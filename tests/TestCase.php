<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Bypass autentikasi: anggap setiap test sudah login sebagai admin
        $user = User::first() ?? User::factory()->create();
        $this->actingAs($user);
    }
}