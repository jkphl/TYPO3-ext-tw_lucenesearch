TYPO3 extension tw_lucenesearch
===============================

Simple and lightweight implementation of the Apache Lucene Index as frontend search solution for TYPO3, built on extbase / fluid, supporting wildcard and fuzzy searches, search term highlighting, indexing of uncached pages, custom search term rewrite hooks and much more without any further software requirements (Java application server, Apache Solr etc.)


Documentation
-------------

A (slightly outdated) online documentation is available in the [TYPO3 Extension Repository](http://docs.typo3.org/typo3cms/extensions/tw_lucenesearch/). The PDF version is available at the [Tollwerk website](https://tollwerk.de/fileadmin/media/manuals/tw_lucenesearch/manual.pdf) or at the [Github repository](https://github.com/tollwerk/TYPO3-ext-tw_lucenesearch/blob/master/doc/manual.pdf).


Requirements
------------

This extension version is compatible with TYPO3 7.5 and above.

**ATTENTION**: Please be aware that there won't be any more TER releases of this extension.


Release history
---------------

#### v2.0.1

* Fixed incompatible view helper declaration (#16, #17)

#### v2.0.0

* TYPO3 8 compatibility
* Uses new Icon API (TYPO3 7.5 and above)

#### v1.6.0

* Multiple bugfixes & contributions
* Suitable for composer mode TYPO3

#### v1.5.0
* Added autocompletion support for frontend searches ([#6](https://github.com/jkphl/TYPO3-ext-tw_lucenesearch/pull/6))
* Added a backend module for index management

Legal
-----

Copyright © 2020 Joschi Kuphal joschi@kuphal.net / @jkphl

Licensed under the terms of the [GPL v2](LICENSE.txt) license.
