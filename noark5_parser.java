import com.arkivarium.Noark5Parser;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileReader;
import java.io.FileWriter;

class noark5_parser {
    public static void main (String[] args) {
	Noark5Parser noark5_parser = new Noark5Parser();    
	File selectedImportFile = new File("n5uttrekk-arkivstruktur.xml");
	File selectedExportFile = new File("noark5_parser-arkivstruktur.xml");
	noark5_parser.parseImport(selectedImportFile);
	noark5_parser.storeExport(selectedExportFile);
    }
}
