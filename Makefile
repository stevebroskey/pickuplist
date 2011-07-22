TARGET=../web
JSMIN=pickuplist.min.js pickuplist-map.min.js pickuplist-aux.min.js
JSDBG=pickuplist.dbg.js pickuplist-map.dbg.js pickuplist-aux.dbg.js
CSS=screen.css reset.css

all: ${JSDBG} ${JSMIN} ${CSS}

%.dbg.js: %.coffee
	coffee -cp $< > $@

%.min.js: %.dbg.js
	closure < $< | uglifyjs -nc > $@

%.css: %.less
	lessc -x $< > $@

deploy: all
	cp -a ./* ${TARGET}/
	cp ./.htaccess ${TARGET}/

clean:
	rm -f ${JSDBG}
	rm -f ${JSMIN}
	rm -f ${CSS}

clean_target:
	rm -rf ${TARGET}/*
	rm -rf ${TARGET}/.htaccess
