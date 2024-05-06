#!/bin/bash

# API endpoint
API_ENDPOINT="http://localhost:2004/api/v1/login"

# Data to send in the POST request
DATA="email=j@h&password=123"

# Make the POST request using curl
response=$(curl -X POST -d "$DATA" -i -c cookies.txt "$API_ENDPOINT")

# Print response headers and cookies
echo "Response Headers:"
echo "$response" | awk 'BEGIN {RS="\r\n\r\n"; FS="\r\n"} NR>1 {print}'
echo
echo "Cookies:"
cat cookies.txt

# Cleanup: Remove cookies file
rm -f cookies.txt
