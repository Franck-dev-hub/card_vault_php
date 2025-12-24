#!/bin/bash

# Test all language letters
for first in {a..z}; do
    for second in {a..z}; do
        lang="$first$second"
        url="https://api.tcgdex.net/v2/$lang/cards/swsh3-136"

        status=$(curl -s -o /dev/null -w "%{http_code}" "$url")

        if [ "$status" = "200" ]; then
            echo "$lang: $status"
        fi
    done
done
