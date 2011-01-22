#!/bin/bash

if [ $# -gt 0 ]
then
	folder=$1
	if [[ "${folder: -1}" != "/" ]]
	then
		folder="${folder}/"
	fi
else
	folder=""
fi

if [ ! -e ${folder}output.po ]
then
	cat > ${folder}output.po << EOF
# SOME DESCRIPTIVE TITLE.
# Copyright (C) YEAR THE PACKAGE'S COPYRIGHT HOLDER
# This file is distributed under the same license as the PACKAGE package.
# FIRST AUTHOR <EMAIL@ADDRESS>, YEAR.
#
#, fuzzy
msgid ""
msgstr ""
"Project-Id-Version: PACKAGE VERSION\n"
"Report-Msgid-Bugs-To: \n"
"POT-Creation-Date: 2009-07-23 23:24+0200\n"
"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\n"
"Last-Translator: FULL NAME <EMAIL@ADDRESS>\n"
"Language-Team: LANGUAGE <LL@li.org>\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
EOF
fi

php functions/builtins/generateFunctionXml.php > functions/builtins/functions.xml1.php

for ITEM in $(find . -name "*.php")
do
	eval xgettext --from-code=UTF-8 -L PHP -o ${folder}output.po --join-existing ${ITEM}
done

rm functions/builtins/functions.xml1.php