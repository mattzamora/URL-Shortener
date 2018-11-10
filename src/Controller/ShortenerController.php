<?php

// src/Controller/ShortenerController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShortenerController extends AbstractController
{
    public function index()
    {
		return $this->render('index.html.twig');
        //return new Response('<!DOCTYPE html><html><body><h1>Symfony is working!</h1></body></html>');
    }
}