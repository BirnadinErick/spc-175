#!/bin/bash
#
# Description: This script combines all Markdown (.md) files in a specified directory into a single README.md file.
# Usage: ./combine_md_files.sh <directory>
# Author: Birnadin Erick (www.methebe.com)
# Copyright (c) 2023 Birnadin Erick. All rights reserved.

# Check if the correct number of command-line arguments is provided
if [ "$#" -ne 1 ]; then
  echo "Usage: $0 <directory>"
  exit 1
fi

directory="$1"
output_file="README.md"

if [ ! -d "$directory" ]; then
  echo "Error: Directory not found."
  exit 1
fi

find "$directory" -maxdepth 1 -type f -name "*.md" | sort | xargs cat > "$output_file"
