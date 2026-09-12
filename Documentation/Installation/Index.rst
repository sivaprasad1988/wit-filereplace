..  include:: /Includes.rst.txt
..  _installation:

============
Installation
============

The extension is required via Composer as a local path package (it lives
under :file:`packages/wit_filereplace` in this project):

..  code-block:: bash

    composer require woit/filereplace:@dev

..  note::

    The composer vendor is ``woit``, not ``wit`` - the ``wit`` namespace was
    already taken on Packagist when this extension was first published, so
    ``woit`` is intentional.

Activate it like any other extension:

..  code-block:: bash

    vendor/bin/typo3 extension:setup

No database tables, TCA or backend module are added - there is nothing
further to install.
