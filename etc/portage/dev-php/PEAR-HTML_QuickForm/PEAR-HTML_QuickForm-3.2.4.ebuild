# Copyright 1999-2005 Gentoo Foundation
# Distributed under the terms of the GNU General Public License v2
# $Header$

inherit php-pear-r1

DESCRIPTION="The PEAR::HTML_QuickForm package provides methods for creating, validating, processing HTML forms."
LICENSE="PHP"
SLOT="0"
KEYWORDS="~x86 ~sparc ~ppc ~ia64 ~amd64"
DEPEND=">=dev-php/PEAR-HTML_Common-1.2.1
		dev-php/PEAR-HTML_Template_Flexy
		dev-php/PEAR-HTML_Template_IT"
# the last two would be option on doc? but I think we should put the docs there
# anyway
IUSE=""
