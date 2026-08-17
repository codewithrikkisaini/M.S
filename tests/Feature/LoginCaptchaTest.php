<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LoginCaptchaTest extends TestCase
{
    public function test_login_requires_a_valid_captcha_answer(): void
    {
        $email = 'captcha.' . uniqid() . '@example.com';

        User::factory()->create([
            'email' => $email,
            'password' => bcrypt('secret123'),
            'status' => 'active',
        ]);

        $response = $this->withSession([
            'login_captcha' => [
                'question' => '8 + 5',
                'answer' => '13',
            ],
        ])->from('/login')->post('/login', [
            '_token' => csrf_token(),
            'email' => $email,
            'password' => 'secret123',
            'captcha' => '12',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('captcha');
    }
}
