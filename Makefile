export:
	./noark5-export --baseurl https://arkivarium.no/noark5v4/ --filename arkivstruktur.xml --username admin@localhost --password password

import:
	./noark5-import --baseurl https://arkivarium.no/noark5v4/ --filename arkivstruktur.xml --username admin@localhost --password password

verify:
	./noark5-verify --baseurl https://arkivarium.no/noark5v4/ --filename arkivstruktur.xml --username admin@localhost --password password
