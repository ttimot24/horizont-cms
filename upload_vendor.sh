#!/bin/bash

lftp -u c2060gitops,password789% ftp://lhdental.hu <<EOF
set ftp:passive-mode on
set cmd:trace yes
set cmd:verbose yes
set xfer:clobber on
mirror -R --verbose ./vendor /new/vendor
bye
EOF