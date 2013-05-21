#!/bin/bash

phpdoc --ignore vendor/ --ignore tests/ -d . -t ../toolbox-docs -p
