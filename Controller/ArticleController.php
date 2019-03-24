<?php
/**
 * Created by iKNSA.
 * User: Khalid Sookia <khalidsookia@gmail.com>
 * Date: 14/03/2019
 * Time: 01:42
 */


namespace Acme\BlogBundle\Controller;


use Acme\BlogBundle\Entity\Article;
use Acme\BlogBundle\Form\ArticleType;
use Acme\BlogBundle\Repository\ArticleRepository;
use IKNSA\SFRequestBundle\Component\HttpFoundation\Request\SFRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ArticleController extends AbstractController
{
    public function create(Request $request): JsonResponse
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if ($request->isMethod(Request::METHOD_POST)) {
            $request = SFRequest::transformJsonBody($request);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($article);
                $entityManager->flush();

                return new JsonResponse([
                    "success" => true,
                    "url" => $this->generateUrl("iknsa_blog_show", ["slug" => $article->getSlug()]),
                    "article" => [
                        "title" => $article->getTitle(),
                        "slug" => $article->getSlug(),
                    ],
                ]);
            }
        }

        return new JsonResponse([
            "success" => true,
            "message" => "created",
            'form' => json_encode($this->get('liform')->transform($form)),
            'article' => $article->getTitle()
        ]);
    }

    public function show(Article $article)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        return new JsonResponse([
            "success" => true,
            "article" => $serializer->serialize($article, 'json')
        ]);
    }

    public function list(int $limit, int $page)
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $articles = $this->getDoctrine()->getRepository(Article::class)
            ->findBy([], null, $limit, ($page - 1) * $limit);

        return new JsonResponse([
            "success" => true,
            "articles" => $serializer->serialize($articles, 'json'),
            "nb_pages" => 3,
            "nb_articles" => 1
        ]);
    }
}
