# TYPO3 Extension `wit_filereplace`

Replaces every frontend image with a static placeholder while running on a
**Development** application context. Real content images (photos, uploaded
media) never render locally, regardless of environment or file source -
useful when the site handles sensitive imagery (e.g. photos of people) that
shouldn't be visible on developer machines, and as a side effect saves
bandwidth from not loading real assets locally.

## What it replaces

- **FAL-processed images** - content elements, `<f:image>`, TypoScript /
  Fluid-rendered backgrounds - via a
  `TYPO3\CMS\Core\Resource\Event\AfterFileProcessingEvent` listener.
- **Raw HTML images** - hardcoded `<img>` markup from RTE bodytext,
  lazy-load `data-src`/`data-srcset` attributes, and inline
  `background-image`/`background` CSS - via a PSR-15 middleware that
  rewrites the final rendered response.

Both mechanisms check `TYPO3\CMS\Core\Core\Environment::getContext()->isDevelopment()`
and are no-ops everywhere else (Production, Testing).

## Installation

```
composer require --dev wit/filereplace
vendor/bin/typo3 extension:setup
```

Installing it as a `require-dev` dependency means it is not even present in
a production build that runs `composer install --no-dev`.

## Configuration

None needed - controlled entirely by `TYPO3_CONTEXT`. See
[`Documentation/Configuration/Index.rst`](Documentation/Configuration/Index.rst)
for how to change the placeholder image.

## Documentation

Full documentation lives in [`Documentation/`](Documentation/Index.rst).

## License

GPL-2.0-or-later, see [LICENSE](LICENSE).
