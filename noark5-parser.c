/* Copyright (c) 2017  Ole Aamot Software

   Permission is hereby granted, free of charge, to any person
   obtaining a copy of this software and associated documentation
   files (the "Software"), to deal in the Software without
   restriction, including without limitation the rights to use, copy,
   modify, merge, publish, distribute, sublicense, and/or sell copies
   of the Software, and to permit persons to whom the Software is
   furnished to do so, subject to the following conditions:

   The above copyright notice and this permission notice shall be
   included in all copies or substantial portions of the Software.
		     
   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
   EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
   MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
   NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS
   BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN
   ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
   CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
   SOFTWARE.
*/

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <glib.h>
#include <libxml/xmlmemory.h>
#include <libxml/parser.h>

typedef struct _Noark5Reference {
  char *recordCreator;
  char *systemType;
  char *systemName;
  char *archive;
} Noark5Reference;

typedef struct _Noark5Dataset {
  char *name;
  char *description;
  Noark5Reference *reference;
} Noark5Dataset;

static void
noark5_reference_parser(Noark5Dataset *dataset, xmlDocPtr doc, xmlNodePtr cur)
{
  xmlNodePtr sub;
}

static void
noark5_dataset_parser(Noark5Dataset *dataset, xmlDocPtr doc, xmlNodePtr cur)
{
  xmlNodePtr sub;
  g_return_if_fail(dataset != NULL);
  g_return_if_fail(doc != NULL);
  g_return_if_fail(cur != NULL);
  sub = cur->xmlChildrenNode;
  while (sub != NULL) {
    printf("DEBUG: %s\n", sub->name);
    if ((!xmlStrcmp(sub->name, (const xmlChar *) "dataset"))) {
      dataset->name = (gchar *) xmlNodeListGetString(doc, sub->xmlChildrenNode, 1);
      fprintf(stdout, "text = %s\n", dataset->name);
      noark5_reference_parser(dataset, doc, cur);
    }
    sub = sub->next;
  }
  return;
}

int main (int argc, char **argv)
{
  xmlDocPtr doc = NULL;
  xmlNodePtr cur = NULL;
  xmlNodePtr sub = NULL;
  Noark5Dataset *dataset, *curr;
  Noark5Reference *reference;
   
  if (argc > 1) {
    doc = xmlReadFile(argv[1], NULL, 0);
    if (doc == NULL) {
      perror("xmlParseFile");
      xmlFreeDoc(doc);
      return 0;
    }
    cur = xmlDocGetRootElement(doc);
    if (cur == NULL) {
      fprintf(stderr, "Empty document\n");
      xmlFreeDoc(doc);
      return 1;
    }
    if (xmlStrcmp(cur->name, (const xmlChar *) "addml")) {
      fprintf(stderr, "Document of wrong type, root node != addml\n");
      xmlFreeDoc(doc);
      return 2;
    }
    curr = g_new0(Noark5Dataset, 1);
	
    curr->name = (gchar *) xmlGetProp(cur, (const xmlChar *)"name");

    printf("%s\n", curr->name);

#if 0
    while (sub != NULL) {
      if ((!xmlStrcmp(sub->name, (const xmlChar *) "dataset"))) {
	curr->name = "dataset";
	fprintf(stdout,"Found a new dataset!\n");
	curr->name = (gchar *) xmlNodeListGetString(doc, sub->xmlChildrenNode, 1);
	noark5_dataset_parser(curr, doc, cur);
      }
      sub = sub->next;
    }
#endif
  } else {
    fprintf(stdout, "noark5-parser FILE\n");
  }
  return 0;
}

  
