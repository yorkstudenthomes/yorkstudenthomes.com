<?php

namespace Starsquare\Twig\Extension;

use Aptoma\Twig\Extension\MarkdownEngine\MichelfMarkdownEngine,
    Michelf\SmartyPants;

class MarkdownEngine extends MichelfMarkdownEngine {

    /**
     * {@inheritdoc}
     */
    public function transform($content) {
        // remove block-level tags
        $markdown = preg_replace('~<p>(.*)</p>~', '$1', parent::transform($content));

        // parse through SmartyPants too
        return SmartyPants::defaultTransform($markdown);
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'Starsquare\Markdown';
    }
}
