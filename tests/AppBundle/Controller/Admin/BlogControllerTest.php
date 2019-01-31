<?php

namespace Tests\Controller\Admin;

use AppBundle\DataFixtures\FixturesTrait;
use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BlogControllerTest extends WebTestCase
{
    use FixturesTrait;
    
    /**
     * @dataProvider getUrlsForRegularUsers
     */
    public function testAccessDeniedForRegularUsers($httpMethod, $url)
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'john_user',
            'PHP_AUTH_PW' => 'kitten'
        ]);

        $client->request($httpMethod, $url);
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function getUrlsForRegularUsers()
    {
        yield ['GET', '/en/admin/post/'];
        yield ['GET', '/en/admin/post/1'];
        yield ['GET', '/en/admin/post/1/edit'];
        yield ['POST', '/en/admin/post/1/delete'];
    }

    public function testAdminBackendHomePage()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'jane_admin',
            'PHP_AUTH_PW' => 'kitten',
        ]);

        $crawler = $client->request('GET', '/en/admin/post/');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertGreaterThanOrEqual(
            1,
            $crawler->filter('body#admin_post_index #main tbody tr')->count(),
            'The backend homepage displays all the available posts.'
        );
    }

    public function testAdminNewPost()
    {
        $postTitle = 'Blog Post Title '.mt_rand();
        $postSummary = $this->getRandomPostSummary();
        $postContent = $this->getPostContent();

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'jane_admin',
            'PHP_AUTH_PW' => 'kitten',
        ]);
        $crawler = $client->request('GET', '/en/admin/post/new');
        $form = $crawler->selectButton('Create post')->form([
            'post[title]' => $postTitle,
            'post[summary]' => $postSummary,
            'post[content]' => $postContent,
        ]);
        $client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        $post = $client->getContainer()->get('doctrine')->getRepository(Post::class)->findOneBy([
            'title' => $postTitle,
        ]);
        $this->assertNotNull($post);
        $this->assertSame($postSummary, $post->getSummary());
        $this->assertSame($postContent, $post->getContent());
    }

    public function testAdminShowPost()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'jane_admin',
            'PHP_AUTH_PW' => 'kitten',
        ]);
        $client->request('GET', '/en/admin/post/1');

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testAdminEditPost()
    {
        $newBlogPostTitle = 'Blog Post Title '.mt_rand();

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'jane_admin',
            'PHP_AUTH_PW' => 'kitten',
        ]);
        $crawler = $client->request('GET', '/en/admin/post/1/edit');
        $form = $crawler->selectButton('Save changes')->form([
            'post[title]' => $newBlogPostTitle,
        ]);
        $client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        /** @var Post $post */
        $post = $client->getContainer()->get('doctrine')->getRepository(Post::class)->find(1);
        $this->assertSame($newBlogPostTitle, $post->getTitle());
    }

    public function testAdminDeletePost()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'jane_admin',
            'PHP_AUTH_PW' => 'kitten',
        ]);
        $crawler = $client->request('GET', '/en/admin/post/1');
        $client->submit($crawler->filter('#delete-form')->form());

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        $post = $client->getContainer()->get('doctrine')->getRepository(Post::class)->find(1);
        $this->assertNull($post);
    }
}