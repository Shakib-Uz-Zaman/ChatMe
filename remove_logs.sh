#!/bin/bash
# Script to replace all console.log statements with comments in home.php

sed -i 's/console\.log(\(.*\))/\/\/ Console log removed: \1/g' home.php
