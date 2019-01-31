<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use AppBundle\Events;
use AppBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Controller used to manage blog content in public part of the site
 * 
 * @Route("/blog")
 */
class BlogController extends Controller
{
    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, name="blog_index")
     * @Route("/rss.xml", defaults={"page": "1", "_format"="xml"}, name="blog_rss")
     * @Route("/page/{page}", defaults={"_format"="html"}, requirements={"page": "[1-9]\d*"}, name="blog_index_paginated")
     * @Method("GET")
     * @Cache(smaxage="10")
     * 
     */
    public function indexAction($page, $_format)
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Post::class)->findLatest($page);

        return $this->render('blog/index.'.$_format.'.twig', ['posts' => $posts]);   
    }

    /**
     * @Route("/posts/{slug}", name="blog_post")
     * @Method("GET")
     */
    public function postShowAction(Post $post)
    {
        if ('dev' === $this->getParameter('kernel.environment')) {
            dump($post, $this->getUser(), new \DateTime());
        }

        return $this->render('blog/post_show.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/comment/{postSlug}/new", name="comment_new")
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @ParamConverter("post", options={"mapping": {"postSlug": "slug"}})
     */
    public function commentNewAction(Request $request, Post $post, EventDispatcherInterface $eventDispatcher)
    {
        $comment = new Comment();
        $comment->setAuthor($this->getUser());
        $post->addComment($comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $event = new GenericEvent($comment);

            $eventDispatcher->dispatch(Events::COMMENT_CREATED, $event);

            return $this->redirectToRoute('blog_post', ['slug' => $post->getSlug()]);
        }

        return $this-render('blog/comment_form_error.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Post $post
     * 
     * @return Response
     */
    public function commentFormAction(Post $post)
    {
        $form = $this->createForm(CommentType::class);

        return $this->render('blog/_comment_form.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/search", name="blog_search")
     * @Method("GET")
     * 
     * @return Response|JsonResponse
     */
    public function searchAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->render('blog/search.html.twig');
        }
        
        $query = $request->query->get('q', '');
        $posts = $this->getDoctrine()->getRepository(Post::class)->findBySearchQuery($query);
        
        $results = [];
        foreach ($posts as $post) {
            $results[] = [
                'title' => htmlspecialchars($post->getTitle()),
                'summary' => htmlspecialchars($post->getSummary()),
                'url' => $this->generateUrl('blog_post', ['slug' => $post->getSlug()]),
            ];
        }

        return $this->json($results);
    }
}
