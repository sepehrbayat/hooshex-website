<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use DOMDocument;
use DOMXPath;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertNumbersToPersian
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (!$this->isHtmlResponse($response)) {
            return $response;
        }

        $content = $response->getContent();
        if ($content === false || $content === null) {
            return $response;
        }

        $response->setContent($this->convertNumbers($content));

        return $response;
    }

    private function isHtmlResponse(Response $response): bool
    {
        $type = $response->headers->get('Content-Type', '');

        return str_contains($type, 'text/html');
    }

    private function convertNumbers(string $content): string
    {
        $latin = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        $document = new DOMDocument('1.0', 'UTF-8');
        $internalErrors = libxml_use_internal_errors(true);
        $document->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING);

        $xpath = new DOMXPath($document);
        /** @var \DOMText $textNode */
        foreach ($xpath->query('//text()[not(ancestor::script) and not(ancestor::style)]') as $textNode) {
            $textNode->nodeValue = str_replace($latin, $persian, $textNode->nodeValue);
        }

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        return $document->saveHTML();
    }
}

