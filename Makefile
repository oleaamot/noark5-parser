check: runparser
	./runparser

log:	noark5-parser.php
	./runparser >noark5-parser.log

run:	check
	./runparser

noark5-parser:	noark5-parser.c
	cc noark5-parser.c -o noark5-parser `pkg-config --cflags --libs glib-2.0 libxml-2.0`

clean:
	rm -vf noark5-parser
