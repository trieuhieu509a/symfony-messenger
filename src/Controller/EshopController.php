<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class EshopController extends AbstractController
{
    /**
     * @Route("/", name="eshop")
     */
    public function index()
    {
        return $this->render('eshop/index.html.twig', [
            'controller_name' => 'EshopController',
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search()
    {
        $search = 'laptops';
        // call database
        sleep(1);
        $result = ' result from database';

        return new Response('Your search results for '.$search.$result);
    }

    /**
     * @Route("/signup-sms", name="signup-sms")
     */
    public function SignUpSMS()
    {
        $phoneNumber = '111 222 333 ';
        // connect to api of external sms service provider
        sleep(2);

        return new Response(sprintf('Your phone number %s succesfully signed up to SMS newsletter!',$phoneNumber));
    }

    /**
     * @Route("/order", name="order")
     */
    public function Order()
    {
        $productId = 243;
        $productName = 'product name';
        $productAmount = 2;
        // save the order in the database

        // send an email to client confirming the order (product name, amount, price, etc.)
        // update warehouse database to keep stock up to date in physical stores
        sleep(4);

        return new Response('You succesfully ordered your product!: '.$productName);
    }
}
