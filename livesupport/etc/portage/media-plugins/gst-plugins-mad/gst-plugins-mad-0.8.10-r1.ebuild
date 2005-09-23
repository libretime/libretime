# Copyright 1999-2005 Gentoo Foundation
# Distributed under the terms of the GNU General Public License v2
# $Header$

inherit gst-plugins eutils

KEYWORDS="alpha amd64 ~arm hppa ia64 -mips ppc ppc64 sparc x86"
IUSE=""

RDEPEND=">=media-libs/libmad-0.15.1b
	>=media-libs/libid3tag-0.15"

DEPEND="${RDEPEND}
	dev-util/pkgconfig"

src_unpack() {

	unpack ${A}
	cd ${S}
	epatch ${FILESDIR}/id3demuxbin-pad-free-fix.patch
}

