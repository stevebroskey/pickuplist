TARGET=../web
JSMIN=pickuplist.min.js pickuplist-map.min.js pickuplist-aux.min.js
JSDBG=pickuplist.dbg.js pickuplist-map.dbg.js pickuplist-aux.dbg.js
CSS=screen.css reset.css
TEMPS=.sass-cache
HTML=index.html

all: ${JSDBG} ${JSMIN} ${CSS} ${HTML} config.php

config.php: config.yaml config.mustache
	mustache $< config.mustache > $@

%.dbg.js: %.coffee
	mustache config.yaml $< | coffee -cps > $@

%.min.js: %.dbg.js
	closure --warning_level QUIET < $< | uglifyjs -nc > $@

%.css: %.sass
	sass --unix-newlines --style compressed $< > $@

%.html: %.haml
	haml --unix-newlines --style indented $< > $@

deploy: all
	cp -a ./* ${TARGET}/
	cp ./.htaccess ${TARGET}/

clean:
	rm -f ${JSDBG}
	rm -f ${JSMIN}
	rm -f ${CSS}
	rm -f ${HTML}
	rm -f config.php
	rm -rf ${TEMPS}

clean_target:
	rm -rf ${TARGET}/*
	rm -rf ${TARGET}/.htaccess
