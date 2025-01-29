<?php

namespace App;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use App\DependencyInjection\D07Extension;

class D07Bundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new D07Extension();
        }
        return $this->extension;
    }
}