<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    /**
     * @dataProvider getPublicUrls
     */
    public function testPublicUrls($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertSame(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode(),
            sprintf('The %s public URL loads correctly.', $url)
        );
    }

    public function testPublicBlogPost()
    {
        $client = static::createClient();
        $blogPost = $client->getContainer()->get('doctrine')->getRepository(Post::class)->find(1);
        $client->request('GET', sprintf('en/blog/posts/%s', $blogPost->getSlug()));

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider getSecureUrls
     */
    public function testSecureUrls($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $response = $client->getResponse();
        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame(
            'http://localhost/en/login',
            $response->getTargetUrl(),
            sprintf('The %s secure URL redirects to the login form.', $url)
        );
    }

    public function getPublicUrls()
    {
        yield ['/'];
        yield ['/en/blog/'];
        yield ['/en/login'];
    }

    public function getSecureUrls()
    {
        yield ['/en/admin/post/'];
        yield ['/en/admin/post/new'];
        yield ['/en/admin/post/1'];
        yield ['/en/admin/post/1/edit'];
    }
}
