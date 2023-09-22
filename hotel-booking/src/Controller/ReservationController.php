<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Reservation;
use \Datetime;

#[Route('/api', name:"api_")]
class ReservationController extends AbstractController
{
    //#[Route('/reservation', name: 'app_reservation')]
    #[Route('/reservation', name: 'reservation_index', methods:'GET')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $query = $entityManager->createQuery('
        select re.id, ro.name as room_name, cl.name as client_name, re.date, re.no_days, re.no_persons 
        from App\Entity\Reservation re         
        inner join App\Entity\Room ro 
        inner join App\Entity\Client cl 
        with re.room_id = ro.id and re.client_id = cl.id
        order by re.id desc
        ');

        $data = $query->execute();
        return $this->json($data);

        /*
        $reservation = $doctrine
        ->getRepository(Reservation::class)
        ->findAll();
        
        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => $reservation->getId(),                
                'room_id' => $reservation->getRoomId(),
                'client_id' => $reservation->getClientId(),
                'date' => $reservation->getDate(),
                'no_days' => $reservation->getNoDays(),
                'no_persons' => $reservation->getNoPersons()
            ];
        }

        return $this->json($data);
       */

       /*
        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
        ]);
        */
    }

    #[Route('/reservation', name: 'reservation_new', methods:'POST')]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $content = json_decode($request->getContent());

        $reservation = new Reservation();
        // without json data
        //$reservation->setRoomId($request->request->get('room_id'));
        
        // with json data
        $reservation->setRoomId($content->room_id);
        $reservation->setClientId($content->client_id);
        // MySQL date format with PHP: Y-m-d H:i:s - 2023-04-11 12:16:18
        // PHP date_format($date,"Y-m-d H:i:s")
        //$reservation->setDate($content->date);
        $reservation->setDate(new DateTime($content->date));
        $reservation->setNoDays($content->no_days);
        $reservation->setNoPersons($content->no_persons);
        $dateTimeNow = new DateTime("now");
        $reservation->setCreatedAt($dateTimeNow); 

        $entityManager->persist($reservation);
        $entityManager->flush();
   
        return $this->json('Created new reservation with id ' . $reservation->getId() . '.');
    }
   
    #[Route('/reservation/{id}', name: 'reservation_show', methods:'GET')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $reservation = $doctrine->getRepository(Reservation::class)->find($id);
   
        if (!$reservation) {
   
            return $this->json('No reservation found for id ' . $id . '.', 404);
        }
   
        $data =  [
            'id' => $reservation->getId(),
            'room_id' => $reservation->getRoomId(),
            'client_id' => $reservation->getClientId(),
            'date' => $reservation->getDate(),
            'no_days' => $reservation->getNoDays(),
            'no_persons' => $reservation->getNoPersons()
        ];

        return $this->json($data);
    }
 
    //#[Route('/reservation/{id}', name: 'reservation_edit', methods:'PUT')]

    #[Route('/reservation/{id}', name:"reservation_edit", methods: ['PUT', 'PATCH'])]      
    public function edit(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $reservation = $entityManager->getRepository(Reservation::class)->find($id);
   
        if (!$reservation) {
            return $this->json('No reservation found for id ' . $id . '.', 404);
        }
         
        $content = json_decode($request->getContent());        

        $reservation->setRoomId($content->room_id);
        $reservation->setClientId($content->client_id);
        //$reservation->setDate($content->date);
        $reservation->setDate(new DateTime($content->date));
        $reservation->setNoDays($content->no_days);
        $reservation->setNoPersons($content->no_persons);
        $dateTimeNow = new DateTime("now");
        $reservation->setUpdatedAt($dateTimeNow); 

        $entityManager->flush();

        $data =  [
            'id' => $reservation->getId(),
            'room_id' => $reservation->getRoomId(),
            'client_id' => $reservation->getClientId(),
            'date' => $reservation->getDate(),
            'no_days' => $reservation->getNoDays(),
            'no_persons' => $reservation->getNoPersons()
        ];
           
        return $this->json($data);
    }
       
    #[Route('/reservation/{id}', name: 'reservation_delete', methods:'DELETE')]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $reservation = $entityManager->getRepository(Reservation::class)->find($id);
   
        if (!$reservation) {
            return $this->json('No reservation found for id ' . $id . '.', 404);
        }
   
        $entityManager->remove($reservation);
        $entityManager->flush();
   
        return $this->json('Deleted reservation with id ' . $id . '.');
    }
}
