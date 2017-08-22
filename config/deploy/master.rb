set :branch, 'master'

# Default deploy_to directory
set :deploy_to, '/var/www/vpn-frontend/master'

set :composer_install_flags, ''
set :composer_roles, :all
set :composer_working_dir, -> { fetch(:release_path) }

set :npm_roles, :all
set :npm_flags, '--silent --no-spin' # default

role :app, %w{jeshkov.ru}

server 'jeshkov.ru',
  user: 'deployer',
  roles: %w{app},
  ssh_options: {
    user: 'deployer', # overrides user setting above
    port: 12222,
    keys: %w(~/.ssh/id_rsa),
    forward_agent: false,
    auth_methods: %w(publickey)
  }

