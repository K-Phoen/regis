web: heroku-php-nginx -C heroku/nginx_app.conf web
inspection_worker: ./bin/console swarrot:consume:analysis_inspection -vvv
github_analysis_status_worker: ./bin/console swarrot:consume:github_analysis_status -vvv
bitbucket_analysis_status_worker: ./bin/console swarrot:consume:bitbucket_analysis_status -vvv
