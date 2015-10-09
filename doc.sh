#!/bin/bash

mkdir -p doc
#phpdoc run --target doc/ --directory src/ --title "Luc PHP Library" --validate --sourcecode
phpdoc run
