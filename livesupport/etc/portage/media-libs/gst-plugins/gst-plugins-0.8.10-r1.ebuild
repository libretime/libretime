# Copyright 1999-2005 Gentoo Foundation
# Distributed under the terms of the GNU General Public License v2
# $Header: /var/cvsroot/gentoo-x86/media-libs/gst-plugins/gst-plugins-0.8.10.ebuild,v 1.10 2005/09/15 11:39:17 agriffis Exp $

# order is important, gnome2 after gst-plugins
inherit gst-plugins gnome2 eutils flag-o-matic libtool

DESCRIPTION="Basepack of plugins for gstreamer"
HOMEPAGE="http://gstreamer.net/"
SRC_URI="http://gstreamer.freedesktop.org/src/${PN}/${P}.tar.bz2"

LICENSE="GPL-2"
KEYWORDS="alpha amd64 ~arm hppa ia64 ~mips ppc ppc64 sparc x86"
IUSE="esd alsa oss"

RDEPEND=">=media-libs/gstreamer-0.8.10
	>=gnome-base/gconf-2"

DEPEND="${RDEPEND}
	>=sys-devel/gettext-0.11.5
	>=dev-util/pkgconfig-0.9"

PDEPEND="oss? ( >=media-plugins/gst-plugins-oss-${PV} )
	alsa? ( >=media-plugins/gst-plugins-alsa-${PV} )
	esd? ( >=media-plugins/gst-plugins-esd-${PV} )"

# we need x for the x overlay to get linked
GST_PLUGINS_BUILD="x xshm"

# overrides the eclass
src_unpack() {

	unpack ${A}
	cd ${S}
	epatch ${FILESDIR}/adder-caps-property.patch
	epatch ${FILESDIR}/adder-duration-fix.patch
	epatch ${FILESDIR}/adder-query.patch
	epatch ${FILESDIR}/switch-fix.patch
	epatch ${FILESDIR}/typefind-smil.patch
}

src_compile() {

	elibtoolize

	# gst doesnt handle optimisations well
	strip-flags
	replace-flags "-O3" "-O2"
	filter-flags "-fprefetch-loop-arrays" # see bug 22249
	if use alpha || use amd64 || use ia64 || use hppa; then
		append-flags -fPIC
	fi

	gst-plugins_src_configure

	emake || die

}

# override eclass
src_install() {

	gnome2_src_install

}

DOCS="AUTHORS INSTALL README RELEASE TODO"

pkg_postinst () {

	gnome2_pkg_postinst
	gst-plugins_pkg_postinst

	echo ""
	einfo "The Gstreamer plugins setup has changed quite a bit on Gentoo,"
	einfo "applications now should provide the basic plugins needed."
	echo ""
	einfo "The new seperate plugins are all named 'gst-plugins-<plugin>'."
	einfo "To get a listing of currently available plugins execute 'emerge -s gst-plugins-'."
	einfo "In most cases it shouldn't be needed though to emerge extra plugins."

}

pkg_postrm() {

	gnome2_pkg_postrm
	gst-plugins_pkg_postrm

}
