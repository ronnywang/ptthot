#
.ifmake (diff)
.if !defined(ORIGIN_PIXCORE_PATH)
.error "必需要指定 ORIGIN_PIXCORE_PATH"
.endif
.endif

GIT?=           /usr/local/bin/git
FIND?=          /usr/bin/find

#
all: update

update:
	${GIT} pull -v

builddoc:
	phpdoc -d Pix/ -t doc/
	${FIND} doc/ -exec sed -i '' 's/iso-8859-1/utf-8/g' {} \;

deploydoc: builddoc
	rsync -av doc/ rsync://testbsd1/pixnet/doc/pixcore-php

test:
	phpunit tests/AllTests.php

diff:
	find Pix tests/Pix -type file -exec diff -ruN {} ${ORIGIN_PIXCORE_PATH}/"{}" \;
