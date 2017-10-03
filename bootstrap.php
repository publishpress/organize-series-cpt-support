<?php

use OrganizeSeries\application\Root;
use OrganizeSeries\domain\model\ClassOrInterfaceFullyQualifiedName;
use OrganizeSeries\CptSupportAddon\domain\Meta;
use OrganizeSeries\CptSupportAddon\domain\services\Bootstrap;


Root::initializeExtensionMeta(
    __FILE__,
    OS_CPT_VER,
    new ClassOrInterfaceFullyQualifiedName(
        Meta::class
    )
);
$fully_qualified_bootstrap_class = new ClassOrInterfaceFullyQualifiedName(Bootstrap::class);
Root::registerAndLoadExtensionBootstrap($fully_qualified_bootstrap_class);
