<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends Controller
{
    /**
     * @Route("/articles")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $data = $request->getContent();
        $article  = $this->get('jms_serializer')->deserialize($data, Article::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/articles/{id}")
     * @Method({"GET"})
     * @param Article $article
     * @return Response
     */
    public function showAction(Article $article)
    {
        $data = $this->get('jms_serializer')->serialize(
            $article, 'json', SerializationContext::create()->setGroups(['detail'])
        );

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/articles", name="articles_create")
     * @Method({"GET"})
     */
    public function listAction()
    {
        $articles = $this->getDoctrine()->getRepository('AppBundle:Article')->findAll();
        $data = $this->get('jms_serializer')->serialize($articles, 'json', SerializationContext::create()->setGroups(['list']));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
