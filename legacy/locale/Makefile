.PHONY: .locale-update build
.DEFAULT: build

SHELL = bash

DOMAIN = libretime
PO_FILE = $(DOMAIN).po
PO_FILES = $(wildcard */LC_MESSAGES/$(PO_FILE))
MO_FILES = $(PO_FILES:.po=.mo)

SRC = application build public

XGETTEXT_ARGS = --default-domain=$(DOMAIN) \
				--language=php \
				--from-code=UTF-8 \
				--no-wrap \
				--sort-by-file

MSGMERGE_ARGS = --no-fuzzy-matching \
				--update \
				--no-wrap \
				--sort-by-file

# This target is run by another Makefile
.locale-update:
	find $(SRC) -name "*.phtml" -o -name "*.php" -type f -print0 | xargs -0 xgettext $(XGETTEXT_ARGS)
	sed -i 's/CHARSET/UTF-8/g' $(PO_FILE)
	find ./locale -name $(PO_FILE) -exec msgmerge $(MSGMERGE_ARGS) "{}" $(PO_FILE) \;
	rm $(PO_FILE)

%.mo: %.po
	msgfmt $< -o $@

build: $(MO_FILES)
