<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class FirstController
{
    public function test()
    {
        return new Response('Une réponse!');
    }
}