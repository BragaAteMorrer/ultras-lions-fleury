<?php

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class EasyAdminExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        // Compatibility shim for legacy easy_admin.yaml files on deployed instances.
    }

    public function getAlias(): string
    {
        return 'easy_admin';
    }
}
