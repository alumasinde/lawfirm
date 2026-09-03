<?php

declare(strict_types=1);

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

final class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u',
        'h2', 'h3', 'h4', 'ul', 'ol', 'li',
        'blockquote', 'a', 'hr',
    ];

    private const DROP_WITH_CONTENT = [
        'script', 'style', 'iframe', 'object', 'embed',
        'svg', 'canvas', 'form', 'input', 'button',
    ];

    public static function sanitize(string $html): string
    {
        $html = trim($html);

        if ($html === '') {
            return '';
        }

        $previous = libxml_use_internal_errors(true);

        try {
            $document = new DOMDocument('1.0', 'UTF-8');
            $document->loadHTML(
                '<!doctype html><html><body><div id="rich-content-root">' . $html . '</div></body></html>',
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );

            $root = $document->getElementById('rich-content-root');

            if ($root === null) {
                return '';
            }

            self::cleanChildren($root);

            $output = '';

            foreach (iterator_to_array($root->childNodes) as $child) {
                $output .= $document->saveHTML($child);
            }

            return trim($output);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private static function cleanChildren(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE) {
                $node->removeChild($child);
                continue;
            }

            if (!$child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                $node->removeChild($child);
                continue;
            }

            // Contenteditable in Chromium commonly uses DIV for Enter.
            // Preserve that block boundary by converting it to our supported P tag
            // instead of unwrapping it and joining adjacent lines together.
            if ($tag === 'div') {
                $paragraph = $child->ownerDocument?->createElement('p');

                if ($paragraph !== null && $child->parentNode !== null) {
                    while ($child->firstChild !== null) {
                        $paragraph->appendChild($child->firstChild);
                    }

                    $child->parentNode->replaceChild($paragraph, $child);
                    self::cleanAttributes($paragraph, 'p');
                    self::cleanChildren($paragraph);
                    continue;
                }
            }

            if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                self::cleanChildren($child);
                self::unwrap($child);
                continue;
            }

            self::cleanAttributes($child, $tag);
            self::cleanChildren($child);
        }
    }

    private static function cleanAttributes(DOMElement $element, string $tag): void
    {
        $href = $tag === 'a' ? trim((string) $element->getAttribute('href')) : '';
        $target = $tag === 'a' ? (string) $element->getAttribute('target') : '';

        foreach (iterator_to_array($element->attributes) as $attribute) {
            $element->removeAttribute($attribute->name);
        }

        if ($tag !== 'a') {
            return;
        }

        if ($href !== '' && preg_match('#^(https?://|mailto:|tel:|/|\\#)#i', $href) === 1) {
            $element->setAttribute('href', $href);
        }

        if ($target === '_blank') {
            $element->setAttribute('target', '_blank');
            $element->setAttribute('rel', 'noopener noreferrer');
        }
    }

    private static function unwrap(DOMElement $element): void
    {
        $parent = $element->parentNode;

        if ($parent === null) {
            return;
        }

        while ($element->firstChild !== null) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }
}
