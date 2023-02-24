.PHONY: .locale-update build
.ONESHELL:

all: clean build

SHELL = bash

DOMAIN = libretime
ISSUE_TRACKER = https://github.com/libretime/libretime/issues
PO_FILE = $(DOMAIN).po
PO_FILES = $(wildcard */LC_MESSAGES/$(PO_FILE))
MO_FILES = $(PO_FILES:.po=.mo)

SRC = application build public

XGETTEXT_ARGS = --default-domain=$(DOMAIN) \
				--msgid-bugs-address=$(ISSUE_TRACKER) \
				--language=php \
				--from-code=UTF-8 \
				--no-wrap \
				--sort-by-file

MSGMERGE_ARGS = --no-fuzzy-matching \
				--update \
				--no-wrap \
				--sort-by-file

update:
	cd ..
	find $(SRC) -name "*.phtml" -o -name "*.php" -type f -print0 | xargs -0 xgettext $(XGETTEXT_ARGS)
	sed -i 's/CHARSET/UTF-8/g' $(PO_FILE)
	find ./locale -name $(PO_FILE) -exec msgmerge $(MSGMERGE_ARGS) "{}" $(PO_FILE) \;
	rm $(PO_FILE)

update-foreach-commit:
	git rebase main -x "$(MAKE) -C legacy/locale update"

check-update: update
	if git status --porcelain -- */LC_MESSAGES/$(PO_FILE) | grep -q '^ M'; then \
		git --no-pager diff -- */LC_MESSAGES/$(PO_FILE); \
		exit 1; \
	fi

%.mo: %.po
	msgfmt $< -o $@

build: $(MO_FILES)

clean:
	@rm -f $(MO_FILES)
