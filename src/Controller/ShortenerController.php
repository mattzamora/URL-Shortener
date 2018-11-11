<?php

// src/Controller/ShortenerController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//QR Code
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\Response\QrCodeResponse;

class ShortenerController extends AbstractController
{
    public function index()
    {
		//file_put_contents($savePath.$fileName, base64_decode($barcode));
		return $this->render('index.html.twig');
        //return new Response('<!DOCTYPE html><html><body><h1>Symfony is working!</h1></body></html>');
    }
	
	public function qr_code_test()
	{
		$qrCode = new QrCode('https://yahoo.com');
		$qrCode->setSize(400);

		// Set advanced options
		$qrCode->setWriterByName('png');
		$qrCode->setMargin(10);
		$qrCode->setEncoding('UTF-8');
		$qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
		$qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
		$qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
		//$qrCode->setLabel('Scan the code', 16, __DIR__.'/../assets/fonts/noto_sans.otf', LabelAlignment::CENTER);
		//$qrCode->setLogoPath(__DIR__.'/../assets/images/symfony.png');
		//$qrCode->setLogoSize(150, 200);
		$qrCode->setRoundBlockSize(true);
		$qrCode->setValidateResult(false);
		//$qrCode->setWriterOptions(['exclude_xml_declaration' => true]);

		// Directly output the QR code
		header('Content-Type: '.$qrCode->getContentType());
		echo $qrCode->writeString();

		// Save it to a file
		$qrcode_name = 'qrcode.png';
		$qrCode->writeFile(__DIR__.'/../../public/images/qrcodes/'.$qrcode_name);
		
		return new Response(__DIR__.'/../../public/images/qrcodes/');
	}
}