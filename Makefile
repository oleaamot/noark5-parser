noark5-parser:	noark5-parser.c
		cc -Wall -Wextra noark5-parser.c -o noark5-parser `pkg-config --cflags --libs libxml-2.0 glib-2.0`

log:	noark5-parser.php
	./runparser >noark5-parser.log

check: runparser
	./runparser

clean:
	rm -vf noark5-parser
