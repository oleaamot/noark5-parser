all:	check

all:	run

build:
	mvn -Dmaven.test.skip=true clean install

run:	build
	mvn -f pom.xml spring-boot:run

clean:
	mvn -Dmaven.test.skip=true clean

jar:
	make -C com/

check: runparser
	./runparser

log:	noark5-parser.php
	./runparser >noark5-parser.log

run:	check
	./runparser

noark5-parser:	noark5-parser.c
	cc noark5-parser.c -o noark5-parser `pkg-config --cflags --libs glib-2.0 libxml-2.0`

noark5_parser:	noark5_parser.java
#	make -C com/
#	javac -cp . noark5_parser.java

clean:
	rm -vf noark5-parser
