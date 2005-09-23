# Copyright 1999-2005 Gentoo Foundation
# Distributed under the terms of the GNU General Public License v2
# $Header$

inherit eutils flag-o-matic

DESCRIPTION="A library for reading and editing audio meta data"
HOMEPAGE="http://developer.kde.org/~wheeler/taglib.html"
SRC_URI="http://developer.kde.org/~wheeler/files/src/${P}.tar.gz"

LICENSE="GPL-2"
SLOT="0"
KEYWORDS="~alpha ~amd64 ~hppa ~ia64 ~ppc ~ppc64 ~sparc ~x86 ~mips"
IUSE="debug"

DEPEND=">=sys-devel/autoconf-2.58"
RDEPEND=""

src_unpack() {
	unpack ${A}
	cd ${S}
	epatch ${FILESDIR}/${P}-memleak-fix2.patch
	epatch ${FILESDIR}/${P}-length-in-microseconds.patch

	rm -rf autom4te.cache
	export WANT_AUTOCONF=2.5
	export WANT_AUTOMAKE=1.7
	aclocal && autoconf && automake || die "autotools failed"
}

src_compile() {
	replace-flags -O3 -O2
	econf `use_enable debug` || die
	emake || die
}

src_install() {
	make install DESTDIR=${D} destdir=${D} || die
}
