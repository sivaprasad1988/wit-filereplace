..  include:: /Includes.rst.txt
..  _introduction:

============
Introduction
============

..  _what-it-does:

What does it do?
=================

Loading real content images while developing locally is unnecessary
bandwidth and, for editorial photos of people, an unwanted privacy exposure
on a local machine. This extension swaps every frontend image for a generic
placeholder graphic whenever the current TYPO3 application context is
``Development`` (or any of its sub-contexts, e.g. ``Development/ddev``).

Two mechanisms cover the two ways images reach the page:

#.  :php:`\Gingco\WitFilereplace\EventListener\ReplaceProcessedImageEventListener`
    listens on :php:`TYPO3\CMS\Core\Resource\Event\AfterFileProcessingEvent`
    and redirects every FAL-processed image (content elements, images
    rendered via :html:`<f:image>`, TypoScript-generated backgrounds) to the
    placeholder.

#.  :php:`\Gingco\WitFilereplace\Middleware\ReplaceImageSourceMiddleware` is a
    PSR-15 middleware that rewrites the final rendered frontend HTML,
    catching images that bypass FAL processing entirely: hardcoded
    :html:`<img>` markup from RTE bodytext, lazy-load ``data-src``/
    ``data-srcset`` attributes, and inline ``background-image``/
    ``background`` CSS declarations.

Both are no-ops outside the Development context, so Staging and Production
render untouched.

..  _requirements:

Requirements
============

*   TYPO3 13.4 or 14.3
