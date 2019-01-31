<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use AppBundle\Utils\Slugger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/post")
 * @Security("has_role('ROLE_ADMIN')")
 */
class BlogController extends Controller 
{
    /**
     * @Route("/", name="admin_index")
     * @Route("/", name="admin_post_index")
     * @Method("GET")
     * 
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Post::class)->findBy(['author' => $this->getUser()], ['publishedAt' => 'DESC']);

        return $this->render('admin/blog/index.html.twig', ['posts' => $posts]);
    }

    /**
     * Creates a new Post entity
     * 
     * @Route("/new", name="admin_post_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Slugger $slugger)
    {
        $post = new Post();
        $post->setAuthor($this->getUser());

        $form = $this->createForm(PostType::class, $post)
            ->add('saveAndCreateNew', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($slugger->slugify($post->getTitle()));

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash('success', 'post.created_successfully');

            if ($form->get('saveAndCreateNew')->isClicked()) {
                return $this->redirectToRoute('admin_post_new');
            }

            return $this->redirectToRoute('admin_post_index');
        }

        return $this->render('admin/blog/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a Post entity
     * 
     * @Route("/{id}", requirements={"id": "\d+"}, name="admin_post_show")
     * @Method("GET")
     */
    public function showAction(Post $post)
    {
        $this->denyAccessUnlessGranted('show', $post, 'Posts can only be shown to their authors');

        return $this->render('admin/blog/show.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     * 
     * @Route("/{id}/edit", requirements={"id": "\d+"}, name="admin_post_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Post $post, Slugger $slugger)
    {
        $this->denyAccessUnlessGranted('edit', $post, 'Posts can only edited by their authors.');

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setSlug($slugger->slugify($post->getTitle()));
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'post.updated_successfully');

            return $this->redirectToRoute('admin_post_edit', ['id' => $post->getId()]);
        }

        return $this->render('admin/blog/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a Post entity
     * 
     * @Route("/{id}/delete", name="admin_post_delete")
     * @Method("POST")
     * @Security("is_granted('delete', post)")
     */
    public function deleteAction(Request $request, Post $post)
    {
        if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            return $this->redirectToRoute('admin_post_index');
        }

        // Delete the tags associated with this blog post. This is done automatically
        // by Doctrine, except for SQLite (the database used in this application)
        // because foreign key support is not enabled by default in SQLite
        $post->getTags()->clear();

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        $this->addFlash('success', 'post.deleted_successfully');

        return $this->redirectToRoute('admin_post_index');
    }
}