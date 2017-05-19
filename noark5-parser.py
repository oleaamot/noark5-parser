#!/usr/bin/python

import xml.etree.cElementTree as ET

tree = ET.ElementTree(file='arkivuttrekk.xml')
root = tree.getroot()
print root.attrib

