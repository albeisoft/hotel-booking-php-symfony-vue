<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Room;
use \Datetime;

#[Route('/api', name:"api_")]
class RoomController extends AbstractController
{
    //#[Route('/room', name: 'app_room')]
    #[Route('/room', name: 'room_index', methods:'GET')]
    public function index(ManagerRegistry $doctrine): Response
    {        
        $entityManager = $doctrine->getManager();

        $query = $entityManager->createQuery('
        select r.id, r.name, r.floor, r.no_places, c.name as category_name
        from App\Entity\Room r 
        inner join App\Entity\Category c 
        with r.category_id = c.id
        order by r.id desc
        ');

        $data = $query->execute();
        return $this->json($data);

        /*
        $rooms = $doctrine
        ->getRepository(Room::class)
        ->findAll();
        */       

        /*
        $data = [];
        foreach ($rooms as $room) {
            $data[] = [
                'id' => $room->getId(),
                'name' => $room->getName(),
                'floor' => $room->getFloor(),
                'no_places' => $room->getNoPlaces(),
                'category_id' => $room->getCategoryId(),
                'note' => $room->getNote()
            ];
        }

        return $this->json($data);
        */        

        /*
        return $this->render('room/index.html.twig', [
            'controller_name' => 'RoomController',
        ]);
        */
    }

    #[Route('/room', name: 'room_new', methods:'POST')]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $content = json_decode($request->getContent());

        $room = new Room();
        // without json data
        //$room->setName($request->request->get('name'));
             
        // with json data
        $room->setName($content->name);
        $room->setFloor($content->floor);
        $room->setNoPlaces($content->no_places);
        $room->setCategoryId($content->category_id);
        $room->setNote($content->note);
        $dateTimeNow = new DateTime("now");
        $room->setCreatedAt($dateTimeNow);

        $entityManager->persist($room);
        $entityManager->flush();
   
        return $this->json('Created new room with id ' . $room->getId() . '.');
    }
   
    #[Route('/room/{id}', name: 'room_show', methods:'GET')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $room = $doctrine->getRepository(Room::class)->find($id);
   
        if (!$room) {
   
            return $this->json('No room found for id ' . $id . '.', 404);
        }
   
        $data =  [
            'id' => $room->getId(),
            'name' => $room->getName(),
            'floor' => $room->getFloor(),
            'no_places' => $room->getNoPlaces(),
            'category_id' => $room->getCategoryId(),
            'note' => $room->getNote()
        ];
           
        return $this->json($data);
    }
 
    //#[Route('/room/{id}', name: 'room_edit', methods:'PUT')]

    #[Route('/room/{id}', name:"room_edit", methods: ['PUT', 'PATCH'])]      
    public function edit(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $room = $entityManager->getRepository(Room::class)->find($id);
   
        if (!$room) {
            return $this->json('No room found for id ' . $id . '.', 404);
        }
         
        $content = json_decode($request->getContent());        

        $room->setName($content->name);
        $room->setFloor($content->floor);
        $room->setNoPlaces($content->no_places);
        $room->setCategoryId($content->category_id);
        $room->setNote($content->note);
        $dateTimeNow = new DateTime("now");
        $room->setUpdatedAt($dateTimeNow); 

        $entityManager->flush();       

        $data =  [
            'id' => $room->getId(),
            'name' => $room->getName(),
            'floor' => $room->getFloor(),
            'no_places' => $room->getNoPlaces(),
            'category_id' => $room->getCategoryId(),
            'note' => $room->getNote()
        ];
           
        return $this->json($data);
    }
       
    #[Route('/room/{id}', name: 'room_delete', methods:'DELETE')]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $room = $entityManager->getRepository(Room::class)->find($id);
   
        if (!$room) {
            return $this->json('No room found for id ' . $id . '.', 404);
        }
   
        $entityManager->remove($room);
        $entityManager->flush();
   
        return $this->json('Deleted room with id ' . $id . '.');
    }
}
