#!/bin/bash

function printError () {
  local indicator="$1"

  echo "ERROR"
  case "${indicator}" in
    "access")
      echo "the user is not member of the webserver's group"
      ;;
    "directory")
      echo "this must be run in the base directory"
      echo "where application, htdocs ... directories are"
      ;;
  esac
}

function printUsage () {
  echo "$0 username webserver-group"
  echo "set access rights on ./keys and ./tests/keys"
  echo "to 2770 username:webserver-group"
  echo "NOTE: username should be member of the webserver's group"
  echo "@see INSTALL"
}

case "$1" in
  "" | "-h" | "--help" | "-?")
    printUsage
    exit
    ;;
esac

if [ ! -d ./keys -o ! -d ./tests/keys ]; then
  printError directory
  exit 1
fi

username=$1
webserver=$2

if [ -z "${username}" -o -z "${webserver}" ]; then
  printUsage
  exit 2
fi

id "${username}" | grep -o '('"${webserver}"')' >/dev/null
if [ $? -ne 0 ]; then
  printError access
  exit 1
fi

dirs="
  ./keys
  ./tests/keys
"
for d in ${dirs}; do
  chown "${username}":"${webserver}" "${d}"
  chmod 2770 "${d}"
done

files="
  pubring.gpg
  pubring.gpg~
  trustdb.gpg
"
for f in ${files}; do
  chown "${username}":"${webserver}" "./tests/keys/${f}"
  chmod 660 "./tests/keys/${f}"
done

echo "done"
