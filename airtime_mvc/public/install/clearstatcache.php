<?php

/* The purpose of this file is get PHP to clear its cache regarding the
 * filesystem layout. See this ticket http://dev.sourcefabric.org/browse/CC-3320 */

clearstatcache(true);
