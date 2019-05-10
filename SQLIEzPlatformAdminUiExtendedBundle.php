<?php

namespace SQLI\EzPlatformAdminUiExtendedBundle;

use SQLI\EzPlatformAdminUiExtendedBundle\DependencyInjection\PolicyProvider\SQLIAdminPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SQLIEzPlatformAdminUiExtendedBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $eZExtension = $container->getExtension('ezpublish');
        $eZExtension->addPolicyProvider(new SQLIAdminPolicyProvider());
    }
}
