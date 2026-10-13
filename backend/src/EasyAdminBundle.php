<?php

namespace App;

use App\DependencyInjection\EasyAdminExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class EasyAdminBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new EasyAdminExtension();
    }
}
