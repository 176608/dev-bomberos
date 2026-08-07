<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Sanitizador HTML por allowlist para contenido administrable
 * (pie_pagina de cuadros, header/footer de secciones, etc.).
 *
 * Elimina scripts, atributos peligrosos (on*, style, srcdoc) y restringe
 * esquemas de URL (href/src) a http(s), rutas relativas, mailto y tel.
 */
class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'b', 'strong', 'i', 'em', 'u', 's', 'sub', 'sup', 'small',
        'ul', 'ol', 'li', 'a', 'span', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'blockquote', 'hr', 'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
        'caption', 'img',
    ];

    private const DANGEROUS_TAGS = [
        'script', 'style', 'iframe', 'object', 'embed', 'applet', 'form', 'input',
        'button', 'select', 'textarea', 'meta', 'link', 'base', 'svg', 'math',
        'template', 'frame', 'frameset', 'video', 'audio', 'source', 'track',
        'dialog', 'portal', 'noscript',
    ];

    public static function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $html = (string) $html;

        // Neutraliza cualquier intento de cerrar el documento o inyectar XML
        $html = preg_replace('#</(?:html|body|head|title|script|style)[^>]*>#i', '', $html) ?? $html;

        libxml_use_internal_errors(true);
        $doc = new DOMDocument('1.0', 'UTF-8');
        $loaded = $doc->loadHTML(
            '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html,
            LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING
        );
        libxml_clear_errors();

        if (!$loaded) {
            return '';
        }

        $body = $doc->getElementsByTagName('body')->item(0);
        if (!$body) {
            return '';
        }

        self::walk($body);

        $out = '';
        foreach ($body->childNodes as $node) {
            $out .= $doc->saveHTML($node);
        }

        return trim($out);
    }

    private static function walk(DOMNode $node): void
    {
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);

                if (in_array($tag, self::DANGEROUS_TAGS, true)) {
                    $child->parentNode?->removeChild($child);
                    continue;
                }

                if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                    self::unwrap($child);
                    continue;
                }

                self::cleanAttributes($child);
                self::walk($child);
            } elseif ($child->nodeType === XML_COMMENT_NODE || $child->nodeType === XML_PI_NODE) {
                $child->parentNode?->removeChild($child);
            }
        }
    }

    /**
     * Sustituye el elemento por sus hijos, conservando el contenido textual.
     */
    private static function unwrap(DOMNode $element): void
    {
        $parent = $element->parentNode;
        if (!$parent) {
            return;
        }
        while ($element->firstChild) {
            $parent->insertBefore($element->firstChild, $element);
        }
        $parent->removeChild($element);
    }

    private static function cleanAttributes(DOMElement $element): void
    {
        $tag = strtolower($element->tagName);

        $keep = ['title'];
        if ($tag === 'a') {
            $keep[] = 'href';
            $keep[] = 'target';
        } elseif ($tag === 'img') {
            $keep[] = 'src';
            $keep[] = 'alt';
            $keep[] = 'width';
            $keep[] = 'height';
        } elseif ($tag === 'td' || $tag === 'th') {
            $keep[] = 'colspan';
            $keep[] = 'rowspan';
            $keep[] = 'align';
        }

        $attrNames = [];
        foreach ($element->attributes as $attr) {
            $attrNames[] = $attr->name;
        }
        foreach ($attrNames as $name) {
            $lower = strtolower($name);
            if (in_array($lower, $keep, true)) {
                continue;
            }
            $element->removeAttribute($name);
        }

        if ($tag === 'a' && $element->hasAttribute('href')) {
            $href = trim($element->getAttribute('href'));
            $element->removeAttribute('href');
            if (self::esUrlSegura($href)) {
                $element->setAttribute('href', $href);
            } else {
                $element->removeAttribute('href');
            }
            if ($element->hasAttribute('target') && strtolower($element->getAttribute('target')) !== '_blank') {
                $element->removeAttribute('target');
            }
        }

        if ($tag === 'img' && $element->hasAttribute('src')) {
            $src = trim($element->getAttribute('src'));
            $element->removeAttribute('src');
            if (self::esUrlSegura($src)) {
                $element->setAttribute('src', $src);
            } else {
                $element->removeAttribute('src');
            }
        }
    }

    private static function esUrlSegura(string $url): bool
    {
        if ($url === '') {
            return false;
        }
        // Bloquea protocolos activos y ofuscaciones (decode de entidades ya hecho por DOMDocument)
        if (preg_match('#(javascript|vbscript|data|file)\s*:#i', $url)) {
            return false;
        }
        // Evita saltos de línea/caracteres de control que puedan evadir el parseo
        if (preg_match('/[\x00-\x1f\x7f\s]/', $url)) {
            return false;
        }
        return (bool) preg_match('#^(https?://|/|#|mailto:|tel:)#i', $url);
    }
}
