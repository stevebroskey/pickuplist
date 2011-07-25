TARGET=../web
JSMIN=pickuplist.min.js pickuplist-map.min.js pickuplist-aux.min.js
JSDBG=pickuplist.dbg.js pickuplist-map.dbg.js pickuplist-aux.dbg.js
CSS=screen.css reset.css
HTML=index.html

all: ${JSDBG} ${JSMIN} ${CSS} ${HTML}

%.dbg.js: %.coffee
	coffee -cp $< > $@

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
	rm -rf .sass-cache

clean_target:
	rm -rf ${TARGET}/*
	rm -rf ${TARGET}/.htaccess
