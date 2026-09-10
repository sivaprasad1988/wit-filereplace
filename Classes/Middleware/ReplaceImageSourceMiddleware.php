<?php

declare(strict_types=1);

namespace Gingco\WitFilereplace\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Gingco\WitFilereplace\PlaceholderUrl;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\Stream;

/**
 * Rewrites every <img> src/srcset and inline background-image in the rendered
 * frontend HTML to a static placeholder while running on a Development
 * instance. Catches images that bypass FAL processing (hardcoded RTE markup,
 * external URLs, etc).
 */
final class ReplaceImageSourceMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        if (!Environment::getContext()->isDevelopment()) {
            return $response;
        }

        $contentType = $response->getHeaderLine('Content-Type');
        if ($contentType !== '' && !str_contains($contentType, 'text/html')) {
            return $response;
        }

        $body = (string)$response->getBody();
        if (!str_contains($body, '<img') && !str_contains($body, 'background-image')) {
            return $response;
        }

        $placeholder = PlaceholderUrl::get();

        $body = preg_replace_callback(
            '/<img\b[^>]*>/i',
            static function (array $matches) use ($placeholder): string {
                $tag = preg_replace('/\s(srcset|data-srcset)=(["\']).*?\2/i', '', $matches[0]);
                return preg_replace(
                    '/\s(src|data-src)=(["\']).*?\2/i',
                    ' $1="' . $placeholder . '"',
                    $tag
                );
            },
            $body
        );

        // inline style="background-image: url(...)" / style="background: url(...)"
        $body = preg_replace(
            '/background(-image)?\s*:([^;"\']*)url\((["\']?).*?\3\)/i',
            'background$1:$2url(' . $placeholder . ')',
            $body
        );

        $stream = new Stream('php://temp', 'rw');
        $stream->write((string)$body);
        $stream->rewind();

        return $response->withBody($stream);
    }
}
