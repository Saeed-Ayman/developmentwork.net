<?php

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'saeed ayman',
        'phone' => '01019604067',
        'password' => 'password',
    ]);

    $response->assertStatus(200);

    $response->assertSee('token');
    $response->assertSee('user');
    $response->assertSee('saeed ayman');
});
