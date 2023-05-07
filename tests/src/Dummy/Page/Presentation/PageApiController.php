<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Dummy\Page\Presentation;

use Ranky\MediaBundle\Tests\Domain\PageFactory;
use Ranky\SharedBundle\Tests\Dummy\Page\Application\PageResponse;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\Page;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/pages', name: 'page_api_')]
class PageApiController extends AbstractController
{


    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $pages = PageFactory::random(10);

        $pagesDto = \array_map(static fn(Page $page) => PageResponse::fromEntity($page), $pages);

        return $this->json($pagesDto);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function get(int|string $id): JsonResponse
    {
        $page    = PageFactory::create((int)$id, 'Title', 'Description');
        $pageDto = PageResponse::fromEntity($page);

        return $this->json($pageDto);
    }

    #[Route('/upload', name: 'upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $file = $request->files->get('file');

        return $this->json([
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
        ], Response::HTTP_CREATED);
    }

    /**
     * @throws \JsonException
     */
    #[Route('', name: 'post', methods: ['POST'])]
    public function post(Request $request): JsonResponse
    {
        $content = \json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $page    = PageFactory::create(1, $content['title'], $content['description']);
        $pageDto = PageResponse::fromEntity($page);

        return $this->json($pageDto, Response::HTTP_CREATED);
    }

    /**
     * @throws \JsonException
     */
    #[Route('/{id}', name: 'put', methods: ['PUT'])]
    public function put(Request $request, string|int $id): JsonResponse
    {
        $content = \json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $page    = PageFactory::create((int)$id, $content['title'], $content['description']);
        $pageDto = PageResponse::fromEntity($page);

        return $this->json($pageDto);
    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(): JsonResponse
    {
        return $this->json([
            'message' => 'Page deleted',
        ]);
    }

}
