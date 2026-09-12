..  include:: /Includes.rst.txt
..  _configuration:

=============
Configuration
=============

There is no TypoScript or TSconfig to set up. The extension is controlled by
the TYPO3 application context plus a handful of settings in the extension
configuration (**Admin Tools > Settings > Extension Configuration >
wit_filereplace**).

..  _configuration-context:

Application context
====================

Both the event listener and the middleware check
:php:`TYPO3\CMS\Core\Core\Environment::getContext()->isDevelopment()` before
doing anything. The context is set via the ``TYPO3_CONTEXT`` environment
variable, e.g. in :file:`.env`:

..  code-block:: bash

    TYPO3_CONTEXT="Development/ddev"

Any context matching or descending from ``Development`` triggers the
replacement; ``Production`` and ``Testing`` are left untouched.

..  _configuration-settings:

Extension settings
===================

restrictToFrontend
-------------------

:Type: boolean
:Default: enabled

When enabled (default), only frontend requests get the placeholder swap.
Backend previews - e.g. the page module's content element preview - always
show the real image, so editors can still verify what they uploaded.

Disable it to placeholder-swap everywhere, including backend previews.

excludedCTypes
--------------

:Type: string (comma-separated CTypes)
:Default: empty

Content element CTypes listed here keep their real images on Development
instances instead of the placeholder, e.g.:

..  code-block:: none

    image,textmedia

This is implemented via TypoScript that :php:`Woit\WitFilereplace\EventListener\AddRealImageTypoScript`
registers for every listed CType, after content-blocks' own default
TypoScript (so the addition isn't wiped out by content-blocks'
:typoscript:`=< lib.contentBlock` copy). For each excluded CType it:

#.  Pushes a render-time flag (:php:`Woit\WitFilereplace\RealImageState`)
    via that CType's :typoscript:`dataProcessing`, before its Fluid template
    (and any :html:`<f:image>` inside it) renders.
#.  Pops the flag again via :typoscript:`stdWrap.postUserFunc`, once the
    CType has fully rendered.
#.  Wraps the CType's rendered output in
    :html:`<!--wit-filereplace:real-->...<!--/wit-filereplace:real-->`
    markers, so :php:`Woit\WitFilereplace\Middleware\ReplaceImageSourceMiddleware`
    skips hardcoded :html:`<img>`/``background-image`` replacement inside
    that content element too.

Only CTypes whose rendering flows through TypoScript (classic
FLUIDTEMPLATE-based CTypes, and content-blocks content elements) are
supported. There is no built-in mechanism to exclude individual content
element *records* - the setting applies to a CType as a whole.

placeholderImage
------------------

:Type: string (file path)
:Default: empty (uses the shipped SVG)

Path to a custom placeholder image, either relative to the site
(``fileadmin/placeholder.jpg``) or as an ``EXT:`` reference
(``EXT:my_sitepackage/Resources/Public/Images/placeholder.png``). Leave
empty to use the image shipped with the extension.

..  code-block:: none

    EXT:wit_filereplace/Resources/Public/Images/placeholder.svg

If the configured path doesn't resolve to an existing file, the shipped
default is used instead. Either way, the resolved URL is cache-busted with
the file's own modification time (``?v=<mtime>``) in
:php:`Woit\WitFilereplace\PlaceholderUrl::get()`, so a new image is picked
up immediately without a manual TYPO3 cache flush - only a hard browser
reload is needed to bypass the browser's own image cache.
