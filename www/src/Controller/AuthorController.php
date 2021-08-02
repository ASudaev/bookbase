<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Traits\ControllerJsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorController extends AbstractController
{
    use ControllerJsonResponse;

    /**
     * @Route("/author/search/{name}", name="author_search", methods={"GET"})
     * @param AuthorRepository $authorRepository
     * @param string $name
     *
     * @return Response
     */
    public function search(AuthorRepository $authorRepository, string $name): Response
    {
        if (!isset($name) || (trim($name) === ''))
        {
            return $this->response([]);
        }

        return $this->response(
            $authorRepository->findByName(trim($name))
        );
    }

    /**
     * @Route("/author/{id}", name="author_by_id", methods={"GET"})
     * @param AuthorRepository $authorRepository
     * @param string $id
     *
     * @return Response
     */
    public function authorById(AuthorRepository $authorRepository, string $id): Response
    {
        if (!isset($id) || !is_numeric($id))
        {
            return $this->response(null);
        }

        return $this->response(
            $authorRepository->findById($id)
        );
    }

    /**
     * @Route("/author/create", name="author_create", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param AuthorRepository $authorRepository
     *
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function create(Request $request, EntityManagerInterface $entityManager, AuthorRepository $authorRepository): Response
    {
        $name = $request->request->get('name');
        if (!isset($name) || (trim($name) === ''))
        {
            return $this->response(['error' => 'Author name not specified']);
        }

        $name = trim($name);
        $cloneFound = $authorRepository->findByNameStrict($name);
        if ($cloneFound)
        {
            return $this->response(['error' => 'Author "' . $cloneFound->getName() . '" already exists']);
        }

        $author = new Author();
        $author->setName($name);
        $entityManager->persist($author);
        $entityManager->flush();

        return $this->response($author);
    }
}
