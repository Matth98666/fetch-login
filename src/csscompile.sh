#!/bin/bash
# Copyright SYRADEV - Regis TEDONE - 2023
# Compile Login scss files

declare sourceSCSS="./scss/"
declare destinationCSS="../public/css/"

for file in "$sourceSCSS"*.scss; do
  sourceFile=$(basename "$file")
  sourceFilenoExt="${sourceFile%%.*}"
  sourcefileFirstChar="${sourceFile:0:1}"
  if [[ ($sourcefileFirstChar != "_") && ($sourceFilenoExt != "bs5") ]];  then
    echo Treating "$sourceFile":
    sass "$file" $destinationCSS"$sourceFilenoExt".min.css --style=compressed
    echo "$sourceFile" treated.
  fi
done
echo -------------------------------------
echo Login SCSS Compilation done!