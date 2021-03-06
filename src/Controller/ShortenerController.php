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

use Symfony\Component\HttpFoundation\Request;

//Print in the Url object with works with the database
use App\Entity\Url;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

class ShortenerController extends AbstractController
{
    public function index()
    {
		return $this->render('index.html.twig');
        //return new Response('<!DOCTYPE html><html><body><h1>Symfony is working!</h1></body></html>');
    }
	
	public function ajaxAction(Request $request, EntityManagerInterface $entityManager)
	{
	   $longurl_input = $request->request->get('longurl');
	   $repository = $this->getDoctrine()->getRepository(Url::class);
	   
	   
	   //Check for an already existing short URL matching the input URL
	   $long_url_host = parse_url($longurl_input, PHP_URL_HOST);
	   if ($long_url_host == $request->getHttpHost()){
		   $possible_slug = parse_url($longurl_input, PHP_URL_PATH);
		   if (strlen($possible_slug)>1){
			    $possible_slug = substr($possible_slug, 1);  // remove the slash from /path in a https://host.com/path
				
				$existing_url = $repository->findOneBy(['short_stub' => $possible_slug]);
				
				//If a matching slug is found, return that info, otherwise continue to generate a slug
				if ($existing_url){
					$short_stub = $possible_slug;
					$baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
					$short_url = $baseurl.'/'.$short_stub;
					$analytics_url = $baseurl.'/view/'.$short_stub;
					return $this->render('confirmation-area.html.twig', array('short_url' =>$short_url, 'analytics_url' => $analytics_url, 'short_stub' => $short_stub ));
				}
		   }
		   
	   }
	   
	   if(!$longurl_input){
		   $reply_message="Failed to grab data";
	   } 
	   else {
			$unique_stub = False;
			
			//generate new stubs until one is unique
			while(!$unique_stub){
				$new_stub = generate_random_stub();
				
				// check that the new_stub is unique
				$existing_url = $repository->findOneBy(['short_stub' => $new_stub]);
				if (!$existing_url){
					$unique_stub = True;
				}
			}
			
			$short_stub=$new_stub;
			
			//Declare the created_on date
			//date_default_timezone_set('America/New_York');
			//$date = date('m/d/Y h:i:s a', time());
			
			
			//Create QR Code
			$baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
			$short_url = $baseurl.'/'.$short_stub;
			$analytics_url = $baseurl.'/view/'.$short_stub;
			
			$qrCode = new QrCode($short_url);
			$qrCode->setSize(400);

			// Set advanced options
			$qrCode->setWriterByName('png');
			$qrCode->setMargin(10);
			$qrCode->setEncoding('UTF-8');
			$qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
			$qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
			$qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
			$qrCode->setRoundBlockSize(true);
			$qrCode->setValidateResult(false);

			// Save QR Code image to a file
			$qrcode_name = $short_stub.'.png';
			$qr_code_address ="images/qrcodes/".$qrcode_name;
			$qrCode->writeFile(__DIR__.'/../../public/'.$qr_code_address);
			
			//Create and save the Url object in the database
			$url = new Url();
			$url->setLongUrl($longurl_input);
			$url->setShortStub($short_stub);
			$url->setQrCodeAddress($qr_code_address);
			$url->setRedirectCount(0);
			$url->setCreatedOn(new DateTime());
			$entityManager->persist($url);

			// actually executes the queries (i.e. the INSERT query)
			$entityManager->flush($url);
			
			return $this->render('confirmation-area.html.twig', array('short_url' =>$short_url, 'analytics_url' => $analytics_url, 'short_stub' => $short_stub ));
	
	   }

	   return new Response( $reply_message );
	}
	
	public function ajaxVanity(Request $request, EntityManagerInterface $entityManager){
		$vanity_input = $request->request->get('vanity');
		$slug_id = $request->request->get('slug_id');
		
		$repository = $this->getDoctrine()->getRepository(Url::class);
		$conflicting_url = $repository->findOneBy(['vanity' => $vanity_input]);
		
		if($conflicting_url){
			return new Response("That vanity URL is take. Please start over.");
		}
		
		$existing_url = $repository->findOneBy(['short_stub' => $slug_id]);
		
		if ($existing_url){
			//Update the Vanity URL
			$existing_url->setVanity($vanity_input);	
			$entityManager->persist($existing_url);
			$entityManager->flush($existing_url);
			
			$baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
			$reply_message = "<p>New vanity url: ".$baseurl.'/'.$vanity_input."</p>";
			return new Response($reply_message);
		}
		else{
			 throw $this->createNotFoundException('This short URL does not exist');
		}
		
	}
	
	
	public function do_redirect($slug,  EntityManagerInterface $entityManager){
		$repository = $this->getDoctrine()->getRepository(Url::class);
		$existing_url = $repository->findOneBy(['short_stub' => $slug]);
		
		//Look for a vanity URL in not a short URL
		if(!$existing_url){
			$existing_url = $repository->findOneBy(['vanity' => $slug]);
		}
		
		if ($existing_url){
			$long_url = $existing_url->getLongUrl();
			$current_count = $existing_url->getRedirectCount();
			$existing_url->setRedirectCount($current_count+1); //Increment view count
			
			$entityManager->persist($existing_url);
			$entityManager->flush($existing_url);
			
			return $this->redirect($long_url);
		}
		else{
			 throw $this->createNotFoundException('This short URL does not exist');
		}
	}
	
	public function view_details($slug, EntityManagerInterface $entityManager, Request $request){
		$repository = $this->getDoctrine()->getRepository(Url::class);
		$existing_url = $repository->findOneBy(['short_stub' => $slug]);
		
		$baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
		$short_url = $baseurl.'/'.$slug;
		
		$long_url = $existing_url->getLongUrl();
		$current_count = $existing_url->getRedirectCount();
		$vanity=$baseurl.'/'.$existing_url->getVanity();
		if (!$vanity){
			$vanity="None";
		}
		
		$qr_code_address = $existing_url->getQrCodeAddress();
		
		$created_on = $existing_url->getCreatedOn()->format('Y-m-d H:i:s');
		
		return $this->render('view/index.html.twig', array('long_url' => $long_url, 'current_count' => $current_count, 'vanity_url' => $vanity, 'qr_code_address' => $qr_code_address, 'created_on' => $created_on, 'short_url' =>$short_url, 'base_url' => $baseurl ));
		
	}
}

function generate_random_stub(){
	 $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
	 $string = '';
	 $random_string_length = rand(5,9);  # Set a stub length to be between 5 and 9 characters
	 
	 $max = strlen($characters) - 1;
	 for ($i = 0; $i < $random_string_length; $i++) {
		  $string .= $characters[mt_rand(0, $max)];
	 }
	 
	 return $string;
}