<?php

// extensions
if (class_exists('Gitiki\CodeHighlight\CodeHighlightServiceProvider')) {
    $app->register(new Gitiki\CodeHighlight\CodeHighlightServiceProvider());

    // $app['code_highlight'] = [
    //     'style' => 'tomorrow',
    // ];
}

if (class_exists('Gitiki\Redirector\RedirectorServiceProvider')) {
    $app->register(new Gitiki\Redirector\RedirectorServiceProvider());
}
