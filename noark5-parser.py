#!/usr/bin/python
"""
Noark5 XML parser
"""
import pprint
#"""DOM parsing"""
#import xml.dom.minidom
#from xml.dom.minidom import Node
#doc = xml.dom.minidom.parse('arkivuttrekk.xml')

from xml.etree.ElementTree import parse
tree = parse('arkivuttrekk.xml')
mapping = {}
i = 0
for addml in tree.findall('addml'):
    name = addml.attrib['name']
    for dataset in addml.findall('dataset'):
        for description in dataset.findall('description'):
            mapping[name] = description.text
pprint.pprint(mapping)
# """DOM parsing"""
# for node in doc.getElementsByTagName("addml"):
#    name = node.getAttribute("name")
#    dataset = node.getElementsByTagName("dataset")
#    for dset in dataset:
#        dsets = dset.getElementsByTagName("description")
#        mapping[name] = dsets.text
#    for reference in dataset:
#        references = reference.getElementsByTagName("reference")
#    for dataobject in dataset:
#        dataobjects = dataobject.getElementsByTagName("dataObjects")
#pprint.pprint(mapping)
