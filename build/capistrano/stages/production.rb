server 'gitstash.io', user: 'deploy', roles: [ 'db', 'app','web' ]

set :deploy_to, "/wwwroot/gitstash.io/"
set :branch, ENV["BRANCH"] || "master"
