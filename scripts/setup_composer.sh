#!/bin/bash
cd "${0%/*}/.."
WEBDIR=../../../$1
cp -r jumpfiles/* "$WEBDIR"
cp -rn schema "$WEBDIR/dataConnectorV2"
cp -n .htaccess "$WEBDIR/dataConnectorV2"
ln -fs "$PWD/client" "$WEBDIR/dataConnectorV2"
