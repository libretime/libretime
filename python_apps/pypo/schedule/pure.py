"""
    schedule.pure
    ~~~~~~~~~

    This module exports a set of 'pure' common functions with no side-effects 
    that may be used by various parts of the pypo scheduler.

    :author: (c) 2012 by Martin Konecny.
    :license: GPLv3, see LICENSE for more details.
"""

import re

def version_cmp(version1, version2):
    """Compare version strings such as 1.1.1, and 1.1.2. Returns the same as
    Python built-in cmp. That is return value is negative if x < y, zero if 
    x == y and strictly positive if x > y."""
    def normalize(v):
        return [int(x) for x in re.sub(r'(\.0+)*$','', v).split(".")]
    return cmp(normalize(version1), normalize(version2))


def date_interval_to_seconds(interval):
    """Convert timedelta object into int representing the number of seconds. If
    number of seconds is less than 0, then return 0."""
    seconds = ((interval.microseconds + 
               (interval.seconds + interval.days * 24 * 3600) * 10 ** 6) 
                    / float(10 ** 6))

    return seconds
