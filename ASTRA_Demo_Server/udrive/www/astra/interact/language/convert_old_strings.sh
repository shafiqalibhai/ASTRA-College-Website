#!/bin/sh
if test $# -eq 0 
then
	echo 'Usage: concert_old_strings.sh language_folder_name'
	exit 1
fi

mkdir "$1_old";
mkdir "$1_old/strings";
	
for i in `ls $1/strings|grep -v 'CVS'`
	do
	echo "Coverting file: $1/strings/$i"  
	grep -v '//' "$1/strings/$i" | grep '=' | sed -e "s/\\\\'/{SQUOTE!}/g" | sed -e "s/[^']*'\([^']*\)'\][[:space:]]*=[[:space:]]*'\([^']*\)'.*/\1=\2/" | sed -e "s/{SQUOTE!}/'/g" > "$1/strings/`echo $i|sed -e 's/\.inc\.php/\.txt/'`"

	mv "$1/strings/$i" "$1_old/strings/"
done
echo "";
echo "Please check the converted files for any lines starting with $  (odd lines that need manual fixing)"
exit 0
