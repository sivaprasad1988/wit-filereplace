<?php

declare(strict_types=1);

namespace Woit\WitFilereplace\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Woit\WitFilereplace\PlaceholderUrl;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\Stream;

/**
 * Rewrites every <img> src/srcset and inline background-image in the rendered
 * frontend HTML to a static placeholder while running on a Development
 * instance. Catches images that bypass FAL processing (hardcoded RTE markup,
 * external URLs, etc). Content elements whose CType is on the excluded-CType
 * allowlist are wrapped in markers by AddRealImageTypoScript and left
 * untouched here.
 */
final class ReplaceImageSourceMiddleware implements MiddlewareInterface
{
    private const REAL_IMAGE_MARKER_PATTERN = '/(<!--wit-filereplace:real-->.*?<!--\/wit-filereplace:real-->)/s';

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

        $segments = preg_split(self::REAL_IMAGE_MARKER_PATTERN, $body, -1, PREG_SPLIT_DELIM_CAPTURE);
        $body = implode('', array_map(
            static function (string $segment) use ($placeholder): string {
                if (str_starts_with($segment, '<!--wit-filereplace:real-->')) {
                    return preg_replace(
                        ['/^<!--wit-filereplace:real-->/', '/<!--\/wit-filereplace:real-->$/'],
                        '',
                        $segment
                    );
                }

                return self::replacePlaceholders($segment, $placeholder);
            },
            $segments
        ));

        $stream = new Stream('php://temp', 'rw');
        $stream->write((string)$body);
        $stream->rewind();

        return $response->withBody($stream);
    }

    private static function replacePlaceholders(string $html, string $placeholder): string
    {
        $html = preg_replace_callback(
            '/<img\b[^>]*>/i',
            static function (array $matches) use ($placeholder): string {
                $tag = preg_replace('/\s(srcset|data-srcset)=(["\']).*?\2/i', '', $matches[0]);
                return preg_replace(
                    '/\s(src|data-src)=(["\']).*?\2/i',
                    ' $1="' . $placeholder . '"',
                    $tag
                );
            },
            $html
        );

        // inline style="background-image: url(...)" / style="background: url(...)"
        return preg_replace(
            '/background(-image)?\s*:([^;"\']*)url\((["\']?).*?\3\)/i',
            'background$1:$2url(' . $placeholder . ')',
            $html
        );
    }
}
