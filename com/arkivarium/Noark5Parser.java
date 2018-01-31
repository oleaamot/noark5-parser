package com.arkivarium;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.util.Iterator;
import javax.xml.stream.XMLEventReader;
import javax.xml.stream.XMLInputFactory;
import javax.xml.stream.XMLOutputFactory;
import javax.xml.stream.XMLStreamConstants;
import javax.xml.stream.XMLStreamException;
import javax.xml.stream.XMLStreamWriter;
import javax.xml.stream.events.Attribute;
import javax.xml.stream.events.Characters;
import javax.xml.stream.events.EndElement;
import javax.xml.stream.events.StartElement;
import javax.xml.stream.events.XMLEvent;

/**
 * Parser for Noark 5 arkivstruktur.xml file
 */

public class Noark5Parser {
    public void parseImport(File file) {
	    boolean bSystemID = false;	    
	    boolean bArkivstatus = false;
	    boolean bDokumentmedium = false;
	    boolean bOpprettetDato = false;
	    boolean bAvsluttetDato = false;
	    boolean bAvsluttetAv = false;
	    try {
		    FileReader fileReader = null;
		    fileReader = new FileReader(file);
		    XMLInputFactory factory = XMLInputFactory.newInstance();
		    XMLEventReader eventReader = factory.createXMLEventReader(new FileReader(file.getAbsolutePath()));
		    while(eventReader.hasNext()) {
			    XMLEvent event = eventReader.nextEvent();
			    switch(event.getEventType()) {
			    case XMLStreamConstants.START_ELEMENT:
				    StartElement startElement = event.asStartElement();
				    String qName = startElement.getName().getLocalPart();
				    if (qName.equalsIgnoreCase("arkiv")) {
					    System.out.println("Start Element : arkiv");
					    // Iterator<Attribute> attributes = startElement.getAttributes();
					    // String rollNo = attributes.next().getValue();
					    // System.out.println("Roll No : " + rollNo);
				    } else if (qName.equalsIgnoreCase("systemID")) {
					    bSystemID = true;
				    } else if (qName.equalsIgnoreCase("arkivstatus")) {
					    bArkivstatus = true;
				    } else if (qName.equalsIgnoreCase("dokumentmedium")) {
					    bDokumentmedium = true;
				    } else if (qName.equalsIgnoreCase("opprettetDato")) {
					    bOpprettetDato = true;
				    }
				    else if (qName.equalsIgnoreCase("avsluttetDato")) {
					    bAvsluttetDato = true;
				    }
				    break;
			    case XMLStreamConstants.CHARACTERS:
				    Characters characters = event.asCharacters();
				    if (bSystemID) {
					    System.out.println("systemID: " + characters.getData());
					    bSystemID = false;
				    }
				    if (bArkivstatus) {
					    System.out.println("arkivstatus: " + characters.getData());
					    bArkivstatus = false;
				    }
				    if (bDokumentmedium) {
					    System.out.println("dokumentmedium: " + characters.getData());
					    bDokumentmedium = false;
				    }
				    if (bOpprettetDato) {
					    System.out.println("opprettetdato: " + characters.getData());
					    bOpprettetDato = false;
				    }
				    if (bAvsluttetDato) {
					    System.out.println("avsluttetdato: " + characters.getData());
					    bAvsluttetDato = false;
				    }
				    if (bAvsluttetAv) {
					    System.out.println("avsluttetav: " + characters.getData());
					    bAvsluttetAv = false;
				    }
				    break;
			    case XMLStreamConstants.END_ELEMENT:
				    EndElement endElement = event.asEndElement();
				    if(endElement.getName().getLocalPart().equalsIgnoreCase("arkiv")) {
					    System.out.println("End Element : arkiv");
				    }
				    break;
			    }
		    }
		    fileReader.close();
	    } catch (FileNotFoundException e) {
		    e.printStackTrace();
	    } catch (XMLStreamException e) {
		    e.printStackTrace();
	    } catch (IOException ex) {
		    System.out.println("Failed to open " + file.getAbsolutePath() + "\n");
	    }
    }
    public void storeExport(File file) {
	    XMLOutputFactory factory = XMLOutputFactory.newInstance();
	    try {
		    XMLStreamWriter writer = factory.createXMLStreamWriter(new FileWriter(file.getName()));
		    writer.writeStartDocument("utf-8","1.0");
		    writer.setPrefix("arkiv", "http://www.arkivverket.no/standarder/noark5/arkivstruktur");
		    writer.setDefaultNamespace("http://www.arkivverket.no/standarder/noark5/arkivstruktur");
		    writer.writeStartElement("http://www.arkivverket.no/standarder/noark5/arkivstruktur","arkiv");
		    writer.writeNamespace("xsi", "http://www.w3.org/2001/XMLSchema-instance");
		    writer.writeNamespace("xmlns","http://www.arkivverket.no/standarder/noark5/arkivstruktur");
		    writer.writeNamespace("n5mdk", "http://www.arkivverket.no/standarder/noark5/metadatakatalog");
		    writer.writeAttribute("xsi:schemaLocation","http://www.arkivverket.no/standarder/noark5/arkivstruktur arkivstruktur.xsd");
		    writer.writeStartElement("http://www.arkivverket.no/standarder/noark5/arkivstruktur", "systemID");
		    writer.writeEndElement();
		    writer.writeStartElement("http://www.arkivverket.no/standarder/noark5/arkivstruktur", "tittel");
		    writer.writeEndElement();
		    writer.writeStartElement("http://www.arkivverket.no/standarder/noark5/arkivstruktur", "arkivstatus");
		    writer.writeEndElement();
		    writer.writeStartElement("http://www.arkivverket.no/standarder/noark5/arkivstruktur", "dokumentmedium");
		    writer.writeEndElement();
		    writer.writeStartElement("http://www.arkivverket.no/standarder/noark5/arkivstruktur", "opprettetDato");
		    writer.writeEndElement();
		    writer.writeStartElement("http://www.arkivverket.no/standarder/noark5/arkivstruktur", "opprettetAv");
		    writer.writeEndElement();
		    writer.writeStartElement("http://www.arkivverket.no/standarder/noark5/arkivstruktur", "avsluttetDato");
		    writer.writeEndElement();
		    writer.writeStartElement("http://www.arkivverket.no/standarder/noark5/arkivstruktur", "avsluttetAv");
		    writer.writeEndElement();
		    writer.writeEndElement();
		    writer.writeEndDocument();
		    writer.flush();
		    writer.close();
	    } catch (XMLStreamException e) {
		    e.printStackTrace();
	    } catch (IOException e) {
		    e.printStackTrace();
		    System.out.println("Failed to open " + file.getAbsolutePath() + "\n");
	    }
    }
}
