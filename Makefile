export:
	./noark5-export --baseurl https://nikita.hioa.no/noark5v4/ --filename arkivstruktur.xml --username root@localhost --password password

import:
	./noark5-import --baseurl https://arkivarium.no/noark5v4/ --filename arkivstruktur.xml --username root@localhost --password password

verify:
	./noark5-verify --baseurl https://arkivarium.no/noark5v4/ --filename arkivstruktur.xml --username root@localhost --password password
