package com.arkivarium;
import com.arkivarium.Noark5Parser;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import org.apache.hc.core5.*;
import org.apache.http.HttpEntity;
import org.apache.http.HttpHost;
import org.apache.http.HttpResponse;
import org.apache.http.auth.AuthScheme;
import org.apache.http.auth.AuthScope;
import org.apache.http.auth.UsernamePasswordCredentials;
import org.apache.http.client.AuthCache;
import org.apache.http.client.CredentialsProvider;
import org.apache.http.client.HttpResponseException;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.config.RequestConfig;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.methods.HttpPut;
import org.apache.http.client.protocol.HttpClientContext;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.auth.BasicScheme;
import org.apache.http.impl.client.BasicCredentialsProvider;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.impl.conn.PoolingHttpClientConnectionManager;
import org.apache.http.util.EntityUtils;

class noark5_parser {
    public static void main (String[] args) {
	Noark5Parser noark5_parser = new Noark5Parser();    
	File selectedImportFile = new File("noark5_parser-arkivstruktur.xml");
	File selectedExportFile = new File("noark5_parser-arkivstruktur.xml");
	try {
	    ArrayList<NameValuePair> postParameters;
	    postParameters = new ArrayList<>();
	    postParameters.add(new BasicNameValuePair("username", "admin"));
	    postParameters.add(new BasicNameValuePair("password", "password"));
	    postParameters.add(new BasicNameValuePair("grant_type", "password"));
	    HttpHost targetHost = new HttpHost("localhost", 8092, "http");
	    CredentialsProvider credsProvider = new BasicCredentialsProvider();
	    credsProvider.setCredentials(AuthScope.ANY,
					 new UsernamePasswordCredentials(username, password));
	    AuthCache authCache = new BasicAuthCache();
	    authCache.put(targetHost, new BasicScheme());
	    // Add AuthCache to the execution context
	    final HttpClientContext context = HttpClientContext.create();
	    context.setCredentialsProvider(credsProvider);
	    context.setAuthCache(authCache);
	    noark5_parser.parseImport(selectedImportFile);
	    noark5_parser.storeExport(selectedExportFile);
	} catch (MalformedURLException e) {
	    e.printStackTrace();
	} catch (IOException e) {
	    e.printStackTrace();
	}
    }
}
