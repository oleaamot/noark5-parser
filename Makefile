export:
	./noark5-export --baseurl https://nikita.hioa.no/noark5v4/ --filename arkivstruktur.xml --username s133298@oslomet.no --password password

import:
	./noark5-import --baseurl https://nikita.hioa.no/noark5v4/ --filename arkivstruktur.xml --username s133298@oslomet.no --password password

verify:
	./noark5-verify --baseurl https://nikita.hioa.no/noark5v4/ --filename arkivstruktur.xml --username s133298@oslomet.no --password password
