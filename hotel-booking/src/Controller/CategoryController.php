<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Category;
use \Datetime;

#[Route('/api', name:"api_")]
class CategoryController extends AbstractController
{
    /*
    #[Route('/category', name: 'app_category')]
    public function index(): Response
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }
    */

    /**
    * //@Route("/category", name="category_index", methods={"GET"})
    */

    //#[Route('/category', name: 'app_category')]
    //#[Route('/category', name: 'category_index')]
    #[Route('/category', name: 'category_index', methods:'GET')]
    
    //public function index(): Response
    public function index(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine
            ->getRepository(Category::class)
            ->findAll();
   
        $data = [];
   
        foreach ($categories as $category) {
           $data[] = [
               'id' => $category->getId(),
               'name' => $category->getName(),
               'price' => $category->getPrice(),
           ];
        }
   
   
        return $this->json($data);
    }
     
    /**
     * //@Route("/category", name="category_new", methods={"POST"})
     */
    
    #[Route('/category', name: 'category_new', methods:'POST')]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
   
        $content = json_decode($request->getContent());        

        $category = new Category();
        // without json data
        //$category->setName($request->request->get('name'));
        //$category->setPrice($request->request->get('price'));        
        
        // with json data
        $category->setName($content->name);
        $category->setPrice($content->price);
        /* 
        date('Y-m-d H:m:s');
        date("Y-m-d H:i:s", time());        
        */
        $dateTimeNow = new DateTime("now");
        $category->setCreatedAt($dateTimeNow);     

        $entityManager->persist($category);
        $entityManager->flush();
   
        return $this->json('Created new category with id ' . $category->getId() . '.');
    }
   
    /**
     * //@Route("/category/{id}", name="category_show", methods={"GET"})
     */

    // check if id route param is a number, digit
    // #[Route('/category/{id}', name: 'category_show', requirements: ['id' => '\d+'])]
    // or
    // #[Route('/category/{id<\d+>}', name: 'category_show')]
    // with default value, if is not set in route
    // #[Route('/category/{id}', name: 'category_show', defaults: ['id' => 1])]

    #[Route('/category/{id}', name: 'category_show', methods:'GET')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $category = $doctrine->getRepository(Category::class)->find($id);
   
        if (!$category) {
   
            return $this->json('No category found for id ' . $id . '.', 404);
        }
   
        $data =  [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'price' => $category->getPrice(),
        ];
           
        return $this->json($data);
    }
   
    /**
     * //@Route("/category/{id}", name="category_edit", methods={"PUT", "PATCH"})
     */

    //#[Route('/category/{id}', name: 'category_edit', methods:'PUT')]

    #[Route('/category/{id}', name:"category_edit", methods: ['PUT', 'PATCH'])]      
    public function edit(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);
   
        if (!$category) {
            return $this->json('No category found for id ' . $id . '.', 404);
        }
         
        $content = json_decode($request->getContent());
        $category->setName($content->name);
        $category->setPrice($content->price);
        $dateTimeNow = new DateTime("now");
        $client->setUpdatedAt($dateTimeNow);

        $entityManager->flush();
   
        $data =  [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'price' => $category->getPrice(),
        ];
           
        return $this->json($data);
    }
   
    /**
     * //@Route("/category/{id}", name="category_delete", methods={"DELETE"})
     */
    
    #[Route('/category/{id}', name: 'category_delete', methods:'DELETE')]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $category = $entityManager->getRepository(Category::class)->find($id);
   
        if (!$category) {
            return $this->json('No category found for id ' . $id . '.', 404);
        }
   
        $entityManager->remove($category);
        $entityManager->flush();
   
        return $this->json('Deleted category with id ' . $id . '.');
    }
}
