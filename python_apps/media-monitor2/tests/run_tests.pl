#!/usr/bin/perl 
use strict;
use warnings;

foreach my $file (glob "*.py") {
    system("python $file") unless $file =~ /prepare_tests.py/;
}
