<?php

return [

    /*
    |--------------------------------------------------------------------------
    | University email domains
    |--------------------------------------------------------------------------
    |
    | When somebody registers with an address of one of these domains, the
    | account gets the matching role and can be used right away, for example
    | "khalil.ltaief@student.harbour.space" or "khalil.ltaief@teacher.harbour.space".
    | Every other address can register too, but an admin has to approve it first.
    |
    */

    'student_email_domain' => env('HELPDESK_STUDENT_EMAIL_DOMAIN', 'student.harbour.space'),

    'teacher_email_domain' => env('HELPDESK_TEACHER_EMAIL_DOMAIN', 'teacher.harbour.space'),

];
