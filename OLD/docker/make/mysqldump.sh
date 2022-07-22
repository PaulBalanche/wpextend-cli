#!/bin/bash
mysqldump --host=$1 --user=$2 --password=$3 $4 --result-file=$5