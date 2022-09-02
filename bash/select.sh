#!/bin/bash
select choose in "$@"
do
    echo $REPLY
    break
done