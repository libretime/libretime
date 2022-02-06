.PHONY: all

SHELL = bash

%.zip:
	wget -q $(URL) -O $@

Google.zip: URL=https://github.com/errata-ai/Google/releases/latest/download/Google.zip
Microsoft.zip: URL=https://github.com/errata-ai/Microsoft/releases/latest/download/Microsoft.zip

%: %.zip
	unzip -qq $<
	touch $@

STYLES := ./Microsoft ./Google
STYLES_ZIP := $(addsuffix .zip,$(STYLES))

all: $(STYLES)
