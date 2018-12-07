/* $id$
 *
 * noark5-parser
 *
 * Copyright (C) 2017  Ole Aamot
 *
 * Author: Ole Aamot <oka@oka.no>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <glib.h>
#include <libxml/xmlmemory.h>
#include <libxml/parser.h>

typedef struct _Noark5DataObject {
  char *name;
} Noark5DataObject;

typedef struct _Noark5DataObjects {
  Noark5DataObject *curr;
  Noark5DataObject *prev;
  Noark5DataObject *next;
} Noark5DataObjects;

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
  Noark5DataObjects *dataObjects;
} Noark5Dataset;

static void
noark5_dataobjects_parser(Noark5Dataset *dataset, xmlDocPtr doc, xmlNodePtr cur)
{
  xmlNodePtr sub;
  sub = cur->xmlChildrenNode;
  while (sub != NULL) {
    if ((!xmlStrcmp(sub->name, (const xmlChar *) "dataObject"))) {
      dataset->dataObjects->prev = dataset->dataObjects->curr;
      dataset->dataObjects->curr = g_new0(Noark5DataObject, 1);
      fprintf(stdout,"Found new dataObject!\n");
      dataset->dataObjects->curr->name = (gchar *) xmlGetProp(sub, (const xmlChar *)"name");
      printf("dataObject:%s\n", dataset->dataObjects->curr->name);
    }
    sub = sub->next;    
  }
}

static void
noark5_reference_parser(Noark5Dataset *dataset, xmlDocPtr doc, xmlNodePtr cur)
{
  xmlNodePtr sub;
  sub = cur->xmlChildrenNode;
  while (sub != NULL) {
    if ((!xmlStrcmp(sub->name, (const xmlChar *) "reference"))) {
      dataset->reference = g_new0(Noark5Reference, 1);
      fprintf(stdout,"Found new reference!\n");
    }
    if ((!xmlStrcmp(sub->name, (const xmlChar *) "dataObjects"))) {
      dataset->dataObjects = g_new0(Noark5DataObjects, 1);
      fprintf(stdout,"Found new dataObjects!\n");
      noark5_dataobjects_parser(dataset, doc, sub);
    }
    sub = sub->next;
  }
}

int main (int argc, char **argv)
{
  xmlDocPtr doc = NULL;
  xmlNodePtr cur = NULL;
  xmlNodePtr sub = NULL;
  Noark5Dataset *dataset, *curr;
  Noark5Reference *reference;
  Noark5DataObjects *dataObjects;
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
    sub = cur->xmlChildrenNode;
    while (sub != NULL) {
      if ((!xmlStrcmp(sub->name, (const xmlChar *) "dataset"))) {
	curr->reference = g_new0(Noark5Reference, 1);
	fprintf(stdout,"Found new dataset!\n");
	noark5_reference_parser(curr, doc, sub);
      }
      sub = sub->next;
    }
    free(curr->dataObjects);
    free(curr->reference);
    free(curr->name);
    free(curr);
  } else {
    fprintf(stdout, "noark5-parser FILE\n");
  }
  return 0;
}
