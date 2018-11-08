<?php

// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function index()
    {
        return new Response('<!DOCTYPE html><html><body><h1>Symfony is working!</h1></body></html>');
    }
}