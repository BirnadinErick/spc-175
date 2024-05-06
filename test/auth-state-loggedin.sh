#!/bin/bash

# API endpoints
LOGIN_ENDPOINT="http://localhost:2004/api/v1/login"
AUTH_STATE_ENDPOINT="http://localhost:2004/api/v1/auth-state"

# Data to send in the POST request
DATA="email=j@h&password=123"

# Make the POST request to login endpoint and save cookies
response=$(curl -X POST -d "$DATA" -i -c cookies.txt "$LOGIN_ENDPOINT")

# Print response headers and cookies from login endpoint
echo "Response Headers from Login Endpoint:"
echo "$response" | awk 'BEGIN {RS="\r\n\r\n"; FS="\r\n"} NR>1 {print}'
echo
echo "Cookies from Login Endpoint:"
cat cookies.txt
echo

# Make the GET request to auth-state endpoint using saved cookies
auth_state_response=$(curl -i -b cookies.txt "$AUTH_STATE_ENDPOINT")

# Print response headers and body from auth-state endpoint
echo "Response Headers from Auth-State Endpoint:"
echo "$auth_state_response" | awk 'BEGIN {RS="\r\n\r\n"; FS="\r\n"} NR>1 {print}'
echo
echo "Response Body from Auth-State Endpoint:"
echo "$auth_state_response" | sed '1,/^\r$/d'

# Cleanup: Remove cookies file
rm -f cookies.txt

