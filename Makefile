noark5-parser:	noark5-parser.c
		cc noark5-parser.c -o noark5-parser `pkg-config --cflags --libs libxml-2.0 glib-2.0`
