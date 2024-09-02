<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    'flowbite/dist/flowbite.turbo.min.js' => [
        'version' => '2.4.1',
    ],
    '@fortawesome/fontawesome-free/css/all.min.css' => [
        'version' => '6.6.0',
        'type' => 'css',
    ],
    'stimulus-use' => [
        'version' => '0.52.2',
    ],
    '@floating-ui/dom' => [
        'version' => '1.6.10',
    ],
    '@floating-ui/core' => [
        'version' => '1.6.7',
    ],
    '@floating-ui/utils' => [
        'version' => '0.2.7',
    ],
    '@floating-ui/utils/dom' => [
        'version' => '0.2.7',
    ],
];
