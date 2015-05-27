#!/bin/bash

if [ ! -f ./pubring.gpg ]; then
  echo 'this must be run in the directory where the pubring is'
  exit 1;
fi

gpg --homedir . --import --no-permission-warning *.asc
