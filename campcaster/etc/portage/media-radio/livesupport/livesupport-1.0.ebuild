# Copyright 1999-2005 Gentoo Foundation
# Distributed under the terms of the GNU General Public License v2
# $Header$

inherit eutils flag-o-matic

IUSE=""

DESCRIPTION="LiveSupport is a radio broadcast support tool."
HOMEPAGE="http://livesupport.campware.org/"
SRC_URI="mirror://sourceforge/${PN}/${P}.tar.bz2"

LICENSE="GPL-2"
SLOT="0"
KEYWORDS="~amd64 ~ppc ~sparc ~x86"

RESTRICT="maketest"

DEPEND=">=dev-db/unixODBC-2.2
	media-libs/fontconfig
	>=media-libs/libpng-1.2
	media-libs/jpeg
	dev-libs/openssl
	dev-libs/libxml2
	dev-libs/popt
	media-libs/alsa-lib
	media-libs/libid3tag
	media-libs/libmad
	media-libs/libogg
	media-libs/libvorbis
	>=dev-libs/boost-0.31
	sys-apps/sed
	net-www/apache
	dev-lang/php
	>=dev-php/PEAR-PEAR-1.3.5
	>=dev-php/PEAR-Archive_Tar-1.3.1
	>=dev-php/PEAR-Calendar-0.5.2
	>=dev-php/PEAR-Console_Getopt-1.2
	>=dev-php/PEAR-DB-1.7.6
	>=dev-php/PEAR-File-1.2.0
	>=dev-php/PEAR-File_Find-0.3.1
	>=dev-php/PEAR-HTML_Common-1.2.1-r1
	>=dev-php/PEAR-HTML_QuickForm-3.2.4
	>=dev-php/PEAR-XML_Beautifier-1.1
	>=dev-php/PEAR-XML_Parser-1.2.6
	>=dev-php/PEAR-XML_RPC-1.4.0
	>=dev-php/PEAR-XML_Serializer-0.15
	>=dev-php/PEAR-XML_Util-1.1.1
	>=dev-db/postgresql-7.4
	>=x11-libs/gtk+-2.6.1
	>=dev-cpp/gtkmm-2.5.5
	>=net-misc/curl-7.13.2
	>=dev-cpp/libxmlpp-2.8.1
	=dev-db/libodbc++-0.2.3-r2
	=dev-libs/xmlrpc++-0.7
	=media-libs/gst-plugins-0.8.10-r1
	=media-libs/taglib-1.3.1-r3
	=media-plugins/gst-plugins-mad-0.8.10-r1
	=media-plugins/gst-plugins-ogg-0.8.10
	=media-libs/gstreamer-0.8.10"

src_unpack() {
	unpack ${A}
	cd ${S}

	# these patches are committed to the source as of 2005-09-23
	epatch ${FILESDIR}/taglib-curl-icu.patch
	epatch ${FILESDIR}/prefix-as-make-variable.patch
	epatch ${FILESDIR}/storageServer-docroot.patch
	epatch ${FILESDIR}/setup-install-dirs.patch
	epatch ${FILESDIR}/pg_hba.patch
	# this patch not committed
	epatch ${FILESDIR}/postinstall-config-file.patch

	# toch the tools make stamp, so that tools don't get built
	touch tmp/tools_setup.stamp
}

src_compile() {
	# append -g, otherwise we get 'defined in discared section' linkage errors
	append-flags -g

	econf --with-create-database=no \
		  --with-create-odbc-data-source=no \
		  --with-init-database=no \
		  --with-configure-apache=no \
		  --with-apache-group=apache \
		  --with-www-docroot=${D}/var/www/localhost/htdocs \
		|| die "configure failed"
	emake -j1 || die "make failed"
}

src_install() {
	# to make sure the link from the docroot works
	mkdir -p ${D}/var/www/localhost/htdocs
	emake -j1 prefix=${D}/usr install || die "install failed"
	dodoc doc INSTALL README
}

pkg_postinst() {
	/usr/bin/postInstallStation.sh --directory /usr \
								   --www-root /var/www/localhost/htdocs \
								   --apache-group apache
}

