<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogController extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/blog/');

        $this->assertCount(
            Post::NUM_ITEMS,
            $crawler->filter('article.post'),
            'The homepage displays the right number of posts.'
        );
    }

    public function testNewComment()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'john_user',
            'PHP_AUTH_PW' => 'kitten',
        ]);
        $client->followRedirects();

        // Find first blog post
        $crawler = $client->request('GET', '/en/blog/');
        $postLink = $crawler->filter('article.post > h2 a')->link();

        $crawler = $client->click($postLink);

        $form = $crawler->selectButton('Publish comment')->form([
            'appbundle_comment[content]' => 'Hi, Symfony!',
        ]);
        $crawler = $client->submit($form);

        $newComment = $crawler->filter('.post-comment')->first()->filter('div > p')->text();

        $this->assertSame('Hi, Symfony!', $newComment);
    }


}