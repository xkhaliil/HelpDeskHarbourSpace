<?php

test('links use https when the proxy says the visitor used https', function () {
    $this->withHeaders([
        'X-Forwarded-Proto' => 'https',
        'X-Forwarded-Host' => 'helpdesk.example.com',
    ])
        ->get('/')
        ->assertOk()
        ->assertSee('https://helpdesk.example.com/login');
});
