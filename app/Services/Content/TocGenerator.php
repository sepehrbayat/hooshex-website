<?php

declare(strict_types=1);

namespace App\Services\Content;

use DOMDocument;
use DOMException;
use DOMXPath;
use Illuminate\Support\Facades\Log;

class TocGenerator
{
    /**
     * Parse HTML content and generate Table of Contents
     *
     * @param string $html The HTML content to parse
     * @return array{html: string, toc: array<int, array{id: string, text: string, level: int}>}
     */
    public function parse(string $html): array
    {
        if (empty($html)) {
            return ['html' => $html, 'toc' => []];
        }

        try {
            // Prepend UTF-8 meta tag to preserve Persian/Arabic characters
            $htmlWithMeta = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html;

            // Create DOMDocument with UTF-8 encoding
            $dom = new DOMDocument('1.0', 'UTF-8');
            $internalErrors = libxml_use_internal_errors(true);
            libxml_clear_errors();

            // Load HTML with flags to preserve structure
            $dom->loadHTML(
                mb_convert_encoding($htmlWithMeta, 'HTML-ENTITIES', 'UTF-8'),
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING
            );

            // Create XPath instance
            $xpath = new DOMXPath($dom);

            // Find all h2 and h3 elements
            $headers = $xpath->query('//h2 | //h3');

            $toc = [];
            $index = 1;

            /** @var \DOMElement $header */
            foreach ($headers as $header) {
                // Generate unique ID
                $id = 'section-' . $index;

                // Set id attribute on the header element
                $header->setAttribute('id', $id);

                // Extract text content (ensure UTF-8)
                $text = trim($header->textContent);
                $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

                // Determine level (2 for h2, 3 for h3)
                $level = (int) substr($header->tagName, 1);

                $toc[] = [
                    'id' => $id,
                    'text' => $text,
                    'level' => $level,
                ];

                $index++;
            }

            // Save modified HTML - extract body content if present
            $modifiedHtml = $dom->saveHTML();
            
            // If the document has a body, extract only body content
            $body = $dom->getElementsByTagName('body')->item(0);
            if ($body) {
                $bodyContent = '';
                foreach ($body->childNodes as $node) {
                    $bodyContent .= $dom->saveHTML($node);
                }
                $modifiedHtml = $bodyContent;
            } else {
                // Remove the meta tag we added (it's not needed in output)
                $modifiedHtml = preg_replace('/<meta http-equiv="Content-Type"[^>]*>/i', '', $modifiedHtml);
            }

            // Restore libxml error handling
            libxml_clear_errors();
            libxml_use_internal_errors($internalErrors);

            return [
                'html' => $modifiedHtml ?: $html,
                'toc' => $toc,
            ];
        } catch (DOMException $e) {
            // Log error and return original HTML with empty TOC
            Log::error('TOC Generation Error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return ['html' => $html, 'toc' => []];
        }
    }
}

