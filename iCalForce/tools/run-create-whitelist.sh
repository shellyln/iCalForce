#!/bin/bash

env \
  USERNAME='alice@example.com' \
  PASSWORD='passSecuritytoken' \
  php create-whitelist.php > ../config/whitelist.php
