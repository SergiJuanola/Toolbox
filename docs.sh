#!/bin/bash

phpdoc --ignore vendor/ --validate --ignore tests/ -d . -t ../toolbox-docs -p
