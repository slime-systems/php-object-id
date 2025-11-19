SHELL:=/usr/bin/env bash
MAKEFLAGS+=--always-make

bash:
	nerdctl compose run --rm application bash || true
