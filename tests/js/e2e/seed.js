const execSync = require('child_process').spawnSync;

module.exports = function() {
  const res = execSync('./artisan', ['db:seed', '--env=testing'], {stdio: 'inherit'});
  if (res.status === 1) {
    process.exit(res.status);
  }
}
