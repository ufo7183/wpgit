<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Dbout\WpRestApi;

class RouteAction
{
    /**
     * @param mixed $className
     * @param string $methodName
     * @param array<string> $methods
     * @param mixed $permissionCallback
     */
    public function __construct(
        public readonly mixed $className,
        public readonly string $methodName,
        public readonly array $methods,
        public readonly mixed $permissionCallback,
    ) {
    }
}
