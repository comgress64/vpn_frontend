const path = require('path')
const spawn = require('cross-spawn')
let args = process.argv.slice(2)
const exec = require('child_process').spawn
const execSync = require('child_process').spawnSync

process.env.ENV = 'testing';

require('dotenv').config({ path: '.env.testing' })

if (process.env.APP_TESTING_HOST == null) {
  console.log('You need to specify hostname for test server');
  console.log('Add APP_TESTING_HOST variable to .env.testing file');
  process.exit();
}

// RESET DATABASE
execSync('mysql', ['-u', process.env.DB_USERNAME, `-p${process.env.DB_PASSWORD}`, '-e', `SET FOREIGN_KEY_CHECKS=0;`]);

let res = execSync('mysql', ['-u', process.env.DB_USERNAME, `-p${process.env.DB_PASSWORD}`, '-Nse', `SHOW TABLES`, process.env.DB_DATABASE]);

const tables = res.output.toString().split('\n')
  .filter(table => (!table.includes('mysql') || !table.includes('migrations')) && table)
  .map(table => table.replace(',', ''))
  .forEach((table) => {
    execSync('mysql', ['-u', process.env.DB_USERNAME, `-p${process.env.DB_PASSWORD}`, '-e', `TRUNCATE TABLE ${table}`]);
  });

execSync('mysql', ['-u', process.env.DB_USERNAME, `-p${process.env.DB_PASSWORD}`, '-e', `SET FOREIGN_KEY_CHECKS=1;`]);

// MIGRATE
res = execSync('./artisan', ['migrate'], {stdio: 'inherit'});
if (res.status === 1) {
  process.exit(res.status);
}

// SEED
require('./seed')();

// START PHP SERVER IF NEEDED
const server = args.indexOf('--dev') > -1
  ? null
  : exec('./artisan', ['serve', '-q', '--host', process.env.APP_TESTING_HOST], {stdio: 'inherit', detached: true})

if (args.indexOf('--config') === -1) {
  args = args.concat(['--config', 'tests/js/e2e/nightwatch.config.js'])
}
if (args.indexOf('--env') === -1) {
  args = args.concat(['--env', 'chrome'])
}

const i = args.indexOf('--test')
if (i > -1) {
  args[i + 1] = 'tests/js/e2e/specs/' + args[i + 1]
}

const runner = spawn('./node_modules/.bin/nightwatch', args, {
  stdio: 'inherit'
})

let serverDead = false;

killServer = () => {
  server && !serverDead && process.kill(-server.pid) && (serverDead = true);
};

runner.on('exit', function (code) {
  killServer();
  process.exit(code)
})

runner.on('error', function (err) {
  killServer();
  throw err
})

process.on('exit', function () {
  killServer();
})

process.on('uncaughtException', function (err) {
  killServer();
  throw err
})

process.on('SIGINT', function () {
  killServer();
})
