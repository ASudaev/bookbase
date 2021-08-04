<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Traits\ControllerJsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

        return $this->response($authorRepository->findByName(trim($name)));
    }

    /**
     * @Route("/author/{id}", name="author_by_id", methods={"GET"})
     * @param AuthorRepository $authorRepository
     * @param string $id
     *
     * @return Response
     * @throws NonUniqueResultException
     */
    public function authorById(AuthorRepository $authorRepository, string $id): Response
    {
        if (!isset($id) || !is_numeric($id))
        {
            return $this->response(null);
        }

        return $this->response($authorRepository->findById($id));
    }

    /**
     * @Route("/author/create", name="author_create", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     *
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $author = new Author();
        $author->setName(trim($request->request->get('name')));

        $errors = $validator->validate($author);
        if (count($errors) > 0)
        {
            return $this->response(['error' => (string)$errors]);
        }

        $entityManager->persist($author);
        $entityManager->flush();

        return $this->response($author);
    }
}
