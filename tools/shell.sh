#!/usr/bin/env bash

RELEASE=${RELEASE:-"bookworm"}

docker run --rm -it \
    --user "$(id -u):$(id -g)" \
    --volume "$PWD:/work" \
    --workdir /work \
    --volume "$HOME/.cache:/home/user/.cache" \
    "ghcr.io/libretime/libretime-dev:$RELEASE" \
    "$@"
