<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\FixturesTrait;
use AppBundle\Entity\Tag;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class TagFixtures extends AbstractFixture
{
    use FixturesTrait;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getTagNames() as $index => $name) {
            $tag = new Tag();
            $tag->setName($name);

            $manager->persist($tag);
            $this->addReference('tag-'.$index, $tag);
        }

        $manager->flush();
    }
}
