<?php
// tests/Entity/PostTest.php

namespace App\Tests\Entity;

use App\Entity\Post;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    private Post $post;

    protected function setUp(): void
    {
        $this->post = new Post();
    }

    public function testPostCreation(): void
    {
        // Test du titre
        $this->post->setTitle("Mon premier post");
        $this->assertEquals("Mon premier post", $this->post->getTitle());

        // Test du contenu
        $this->post->setContent("Contenu du post");
        $this->assertEquals("Contenu du post", $this->post->getContent());

        // Test de la date de création
        $this->post->setCreated();
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->post->getCreated());
    }

    public function testPostRequiredFields(): void
    {
        $this->assertNull($this->post->getId());
        $this->assertNull($this->post->getTitle());
        $this->assertNull($this->post->getContent());
        $this->assertNull($this->post->getCreated());
    }
}