#!/bin/bash

# Download env.json file from S3 bucket
aws s3 cp s3://projectjson/env_variables.json /tmp/env.json

# Parse env.json and create .env file
cat /tmp/env.json | jq -r 'to_entries[] | "\(.key)=\(.value)"' > /var/app/current/.env

# Navigate to the Laravel app directory
cd /var/app/current

# Run Laravel Artisan commands
php artisan migrate
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan storage:link