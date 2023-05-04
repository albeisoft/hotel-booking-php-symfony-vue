<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Client;
use \Datetime;

#[Route('/api', name:"api_")]
class ClientController extends AbstractController
{
    //#[Route('/client', name: 'app_client')]
    #[Route('/client', name: 'client_index', methods:'GET')]
    public function index(ManagerRegistry $doctrine): Response    
    {
        $clients = $doctrine
        ->getRepository(Client::class)
        ->findAll();

        $data = [];

        foreach ($clients as $client) {
            $data[] = [
                'id' => $client->getId(),
                'name' => $client->getName(),
                'identification' => $client->getIdentification(),
                'address' => $client->getAddress(),
                'telephone' => $client->getTelephone(),
                'email' => $client->getEmail()
            ];            
        }

        return $this->json($data);

        /*
        return $this->render('client/index.html.twig', [
            'controller_name' => 'ClientController',
        ]);
        */
    }

    #[Route('/client', name: 'client_new', methods:'POST')]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $content = json_decode($request->getContent());

        $client = new Client();
        // without json data
        //$client->setName($request->request->get('name'));
             
        // with json data
        $client->setName($content->name);
        $client->setIdentification($content->identification);
        $client->setAddress($content->address);
        $client->setTelephone($content->telephone);
        $client->setEmail($content->email);
        $dateTimeNow = new DateTime("now");
        $client->setCreatedAt($dateTimeNow);

        $entityManager->persist($client);
        $entityManager->flush();
   
        return $this->json('Created new client with id ' . $client->getId() . '.');
    }

    // check if id route param is a number, digit
    // #[Route('/client/{id}', name: 'client_show', requirements: ['id' => '\d+'])]
    // or
    // #[Route('/client/{id<\d+>}', name: 'client_show')]
    // with default value, if is not set in route
    // #[Route('/client/{id}', name: 'client_show', defaults: ['id' => 1])]

    #[Route('/client/{id}', name: 'client_show', methods:'GET')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $client = $doctrine->getRepository(Client::class)->find($id);
   
        if (!$client) {
   
            return $this->json('No client found for id ' . $id . '.', 404);
        }
   
        $data =  [
            'id' => $client->getId(),
            'name' => $client->getName(),
            'identification' => $client->getIdentification(),
            'address' => $client->getAddress(),
            'telephone' => $client->getTelephone(),
            'email' => $client->getEmail()
        ];
           
        return $this->json($data);
    }
 
    //#[Route('/client/{id}', name: 'client_edit', methods:'PUT')]

    #[Route('/client/{id}', name:"client_edit", methods: ['PUT', 'PATCH'])]      
    public function edit(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $client = $entityManager->getRepository(Client::class)->find($id);
   
        if (!$client) {
            return $this->json('No client found for id ' . $id . '.', 404);
        }
         
        $content = json_decode($request->getContent());        

        $client->setName($content->name);
        $client->setIdentification($content->identification);
        $client->setAddress($content->address);
        $client->setTelephone($content->telephone);
        $client->setEmail($content->email);
        $dateTimeNow = new DateTime("now");
        $client->setUpdatedAt($dateTimeNow);

        $entityManager->flush();
   
        $data =  [
            'id' => $client->getId(),
            'name' => $client->getName(),
            'identification' => $client->getIdentification(),
            'address' => $client->getAddress(),
            'telephone' => $client->getTelephone(),
            'email' => $client->getEmail()
        ];
           
        return $this->json($data);
    }
       
    #[Route('/client/{id}', name: 'client_delete', methods:'DELETE')]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $client = $entityManager->getRepository(Client::class)->find($id);
   
        if (!$client) {
            return $this->json('No client found for id ' . $id . '.', 404);
        }
   
        $entityManager->remove($client);
        $entityManager->flush();
   
        return $this->json('Deleted client with id ' . $id . '.');
    }
}