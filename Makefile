install:
	composer install
	php artisan key:generate
	php artisan migrate
	make assets
	make permissions

prepare:
	composer install
	php artisan migrate
	make assets

npm:
	npm install

assets: npm
	node ./node_modules/webpack/bin/webpack.js

permissions:
	chmod -R 775 ./bootstrap
	chmod -R 775 ./storage

logs:
	tail -n 100 -f storage/logs/laravel.log

lint:
	node ./node_modules/eslint/bin/eslint.js resources --ext js,vue

lintfix:
	node ./node_modules/eslint/bin/eslint.js resources --ext js,vue --fix

test:
	node ./node_modules/karma/bin/karma start tests/js/unit/karma.conf.js --single-run
	node ./tests/js/e2e/runner.js

test-unit:
	node ./node_modules/karma/bin/karma start tests/js/unit/karma.conf.js --single-run

test-e2e:
	node ./tests/js/e2e/runner.js

testdev:
	node ./node_modules/karma/bin/karma start tests/js/unit/karma.conf.js

.PHONY: test
