..  include:: /Includes.rst.txt
..  _configuration:

=============
Configuration
=============

There is no TypoScript, TSconfig or backend module configuration. The
extension is controlled entirely by the TYPO3 application context.

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

..  _configuration-placeholder:

Changing the placeholder image
===============================

The placeholder is a single SVG file shipped with the extension:

..  code-block:: none

    EXT:wit_filereplace/Resources/Public/Images/placeholder.svg

Replace that file to change the placeholder graphic. Its public URL is
resolved once, in :php:`Gingco\WitFilereplace\PlaceholderUrl::get()`, and is
cache-busted with the file's own modification time (``?v=<mtime>``), so a
new image is picked up immediately without a manual TYPO3 cache flush -
only a hard browser reload is needed to bypass the browser's own image
cache.
