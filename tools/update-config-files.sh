#!/usr/bin/env bash

# Keep configuration files in sync with installer/config.yml.

set -u

error() {
  echo >&2 "error: $*"
  exit 1
}

command -v ed > /dev/null || error "ed command not found!"

CONFIG_ORIG_FILEPATH="installer/config.yml"

# set_config <value> <key...>
set_config() {
  value="${1}" && shift

  # Build sed query
  query="/^${1}:/\n"
  while [[ $# -gt 1 ]]; do
    shift
    query+="/${1}:/\n"
  done
  query+="s|\(${1}:\).*|\1 ${value}|\n"
  query+="wq"

  echo -e "$query" | ed --quiet "$CONFIG_FILEPATH" > /dev/null
}

set_docker_config() {
  set_config "postgres" database host
  set_config "rabbitmq" rabbitmq host
  set_config "liquidsoap" playout liquidsoap_host
  set_config "0.0.0.0" liquidsoap server_listen_address
  set_config "icecast" stream outputs .default_icecast_output host
}

CONFIG_FILEPATH="docker/config.yml"
cp "$CONFIG_ORIG_FILEPATH" "$CONFIG_FILEPATH"

set_docker_config

CONFIG_FILEPATH="docker/example/config.yml"
cp "$CONFIG_ORIG_FILEPATH" "$CONFIG_FILEPATH"

set_docker_config
set_config "http://localhost:8080" general public_url
set_config "some_secret_api_key" general api_key
