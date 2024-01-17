#!/bin/bash

# Create an empty JSON object
json_output="{}"

# Loop through all environment variables
while IFS='=' read -r name value ; do
    # Check if the variable starts with LSS_
    if [[ $name == LSS_* ]]; then
        # Extract the key (e.g., "MySQL" from "LSS_MySQL")
        key=$(echo "$name" | sed 's/LSS_//')

        # Parse the value as JSON and add it to the output JSON object
        json_output=$(echo "$json_output" | jq --arg key "$key" --argjson val "$value" '.[$key] = $val')
    fi
done < <(env)

echo "$json_output" > /var/www/html/servers.json

echo "Starting Apache..."
exec apache2-foreground
echo "Apache started."