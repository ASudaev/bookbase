<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Traits\ControllerJsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController extends AbstractController
{
    use ControllerJsonResponse;

    /**
     * @Route("/book/search/{name}", name="book_search", methods={"GET"})
     * @param BookRepository $bookRepository
     * @param string $name
     *
     * @return Response
     */
    public function search(BookRepository $bookRepository, string $name): Response
    {
        if (!isset($name) || (trim($name) === ''))
        {
            return $this->response([]);
        }

        return $this->response(
            $bookRepository->findByName(trim($name))
        );
    }

    /**
     * @Route("/book/{id}", name="book_by_id", methods={"GET"})
     * @param BookRepository $bookRepository
     * @param string $id
     *
     * @return Response
     */
    public function bookById(BookRepository $bookRepository, string $id): Response
    {
        if (!isset($id) || !is_numeric($id))
        {
            return $this->response(null);
        }

        return $this->response(
            $bookRepository->findById($id)
        );
    }

    /**
     * @Route("/book/create", name="book_create", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param AuthorRepository $authorRepository
     *
     * @return Response
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        AuthorRepository $authorRepository
    ): Response
    {
        $book = new Book();
        $book->setNameRu(trim($request->request->get('name_ru')));
        $book->setNameEn(trim($request->request->get('name_en')));

        $errors = $validator->validate($book);
        if (count($errors) > 0)
        {
            return $this->response(['error' => (string)$errors]);
        }

        $authors = array_filter(
            explode(',', $request->request->get('authors')),
            'is_numeric'
        );

        if (count($authors) < 1)
        {
            return $this->response(['error' => 'Authors not specified or invalid']);
        }

        $authorsData = $authorRepository->findByIds($authors);
        if (count($authorsData) < 1)
        {
            return $this->response(['error' => 'Authors not found or invalid']);
        }

        foreach ($authorsData as $author)
        {
            $book->addAuthor($author);
        }

        $entityManager->persist($book);
        $entityManager->flush();

        return $this->response($book);
    }
}
