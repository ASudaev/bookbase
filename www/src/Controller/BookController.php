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
     * @param BookRepository $bookRepository
     * @param AuthorRepository $authorRepository
     *
     * @return Response
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        BookRepository $bookRepository,
        AuthorRepository $authorRepository
    ): Response
    {
        $name_ru = $request->request->get('name_ru');
        if (!isset($name_ru) || (trim($name_ru) === ''))
        {
            return $this->response(['error' => 'Russian name not specified']);
        }

        $name_en = $request->request->get('name_en');
        if (!isset($name_en) || (trim($name_en) === ''))
        {
            return $this->response(['error' => 'English name not specified']);
        }

        $authors = $request->request->get('authors');
        if (!isset($authors) || (trim($authors) === ''))
        {
            return $this->response(['error' => 'Authors not specified']);
        }

        $name_ru = trim($name_ru);
        $name_en = trim($name_en);
        $authors = array_filter(explode(',', $authors), 'is_numeric');
        if (count($authors) < 1)
        {
            return $this->response(['error' => 'Authors not specified or invalid']);
        }

        $authorsData = $authorRepository->findByIds($authors);
        if (count($authorsData) < 1)
        {
            return $this->response(['error' => 'Authors not found or invalid']);
        }

        $book = new Book();
        $book->setNameEn($name_en);
        $book->setNameRu($name_ru);

        foreach ($authorsData as $author)
        {
            $book->addAuthor($author);
        }

        $entityManager->persist($book);
        $entityManager->flush();

        return $this->response($book);
    }
}
