check: runparser
	./runparser

log:	noark5-parser.php
	./runparser >noark5-parser.log

run:	check
	./runparser

clean:
	rm -vf noark5-parser
