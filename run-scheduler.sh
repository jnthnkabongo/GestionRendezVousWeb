#!/bin/bash
export PATH="/Users/jonathankabongo/.config/herd-lite/bin:/usr/local/bin:/usr/bin:/bin"
cd /Users/jonathankabongo/Documents/Rendez-vous/backend_api
php artisan schedule:run >> /tmp/scheduler.log 2>&1
