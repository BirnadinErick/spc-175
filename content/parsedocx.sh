#!/bin/bash

# Iterate over all files in the current directory
for file in *; do
    # Skip directories
    if [ -d "$file" ]; then
        continue
    fi

    # Convert the filename to kebab case
    kebab_case=$(echo "$file" | tr '[:upper:]' '[:lower:]' | tr ' ' '-' | tr -cd 'a-z0-9-.')

    # Rename the file if the name has changed
    if [ "$file" != "$kebab_case" ]; then
        mv "$file" "$kebab_case"
        echo "Renamed: $file -> $kebab_case"
    fi

    # convert the docx to md
    md="${kebab_case%.docx}.md"
    pandoc -f docx -t gfm --extract-media=. $kebab_case -o $md
done


