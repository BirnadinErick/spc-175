#!/bin/bash

# Fetch changes from the remote repository
git fetch origin

# Get a list of remote branches and use fzf for interactive selection
selected_branch=$(git branch -r | grep -v '\->' | sed 's/origin\///' | trim | fzf --prompt="Select branch: " --height 40% --reverse)

# If a branch is selected, checkout to that branch
if [ -n "$selected_branch" ]; then
    git checkout -b "$selected_branch"
    echo "Checked out to branch: $selected_branch"
else
    echo "No branch selected. Exiting script."
fi
