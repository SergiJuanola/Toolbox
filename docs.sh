#!/bin/bash

apigen --source "./" --exclude "*/tests/*" --exclude "*/vendor/*" --destination ../toolbox-docs/ --title "Toolbox Framework"
