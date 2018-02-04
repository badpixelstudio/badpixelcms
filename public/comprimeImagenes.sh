#!/bin/bash
# Compresion sin perdida de ficheros png and jpeg

if [ $# -lt 1 ]; then
	echo "Uso: $(basename $0) dirOrigen  [dirDestino] "
	exit 1
fi

PATH=/usr/local/bin:/bin:/usr/bin
JPEGTRAN=$(which jpegtran)
# JPEGTRANOPTS: Para mayor reduccion usar -copy none . Esto elimina la informacion EXIF.
JPEGTRANOPTS="-optimize -copy none"
PNGCRUSH=$(which pngcrush)
# Para mayor compresion de ficheros png usar la opcion -bruteoutput (mucho mas lento)
PNGCRUSHOPTS="-rem alla -reduce"
#PNGCRUSHOPTS="-rem alla -reduce -bruteoutput"

PATTERN=".*\(png\|jpeg\|jpg\)$"

IMAGEFOLDER="$(readlink -f "$1")"

if [ -n "$2" ]; then
	if [ ! -d "$2" ] ; then
		mkdir -p "$2" || exit 1
	fi
	DESTFOLDER="$(readlink -f "$2")"
else
	DESTFOLDER=""
fi

compressPicture() {

[ -n "$DESTFOLDER" ] && mkdir -p $( dirname ${1/$IMAGEFOLDER/$DESTFOLDER} )

fileType="$(file "$1")"
if echo "$fileType" | grep -q "JPEG" ; then
	compressJPEG "$1"
elif echo "$fileType" | grep -q "PNG" $1; then
	compressPNG "$1"
fi

}

compressJPEG() {
if [ -n "$DESTFOLDER" ]; then
	$JPEGTRAN $JPEGTRANOPTS "$1" > "${1/$IMAGEFOLDER/$DESTFOLDER}"
	RC=$?
else
	$JPEGTRAN $JPEGTRANOPTS "$1" > "$1".tmp && mv "$1".tmp "$1"
	RC=$?
fi
	return $RC
}

compressPNG() {
if [ -n "$DESTFOLDER" ] ; then
	$PNGCRUSH $PNGCRUSHOPTS "$1" "${1/$IMAGEFOLDER/$DESTFOLDER}"
	RC=$?
else
	$PNGCRUSH $PNGCRUSHOPTS "$1" "$1".tmp && mv "$1".tmp "$1"
	RC=$?
fi
	return $RC
}

# Start
if [ ! -d "$IMAGEFOLDER" ]; then
	echo "$IMAGEFOLDER does not exist, exiting"
	exit 1
fi

find $IMAGEFOLDER -iregex $PATTERN | while read i
do
	compressPicture "$i"
done
