# WP theme Soli.nl

## Getting Started
Clone the repository:
```bash
$ cd [~/Sites/pathToWordpress/wp-content/themes/] && git clone git@github.com:IanCStewart/wp-theme-soli.git
```

Install the packages:
```bash
$ cd ~/Sites/pathToWordpress/wp-content/themes/wp-theme-soli && npm i
```

Make sure the following packages are installed in atom:
```bash
apm i linter-php linter-xo editorconfig linter linter-ui-default
```

Install gitmoji-cli for commit messages:
```bash
$ npm i -g gitmoji-cli

# run gitmoji command to make a hook once
$ gitmoji -i

# when making a commit
# add your files
$ git add [files]

# run git commit gitmoji-cli will open the dialog
$ git commit
```

## Development
Make sure docker, docker-compose and docker-machine is installed.

Make dir:
```bash
cd ~/Sites
rm -rf soli && mkdir soli && cd soli
```

Unpak back-ups after placing in `soli` folder:
```bash
gzip -d wp-soli.sql.gz
tar xvf public_html.tar.gz
```

Fix permissions:
```bash
chmod 755 public_html
```

Remove non-git theme:
```bash
rm -rf public_html/wp-content/themes/soli-v2.0/
```

Clone git theme:
```bash
git clone git@github.com:IanCStewart/wp-theme-soli.git public_html/wp-content/themes/soli-v2.0

cp public_html/wp-content/themes/soli-v2.0/docker-compose.yml .
```

Start docker containers:
```bash
docker-compose up
```

Import mysql backup into container:
```bash
docker exec -i soli_mysql mysql -uroot -psoli_sinds1909 wordpress < wp-soli.sql
```

Add the following to the top of functions.php:
```bash
update_option('siteurl', 'http://' . $_SERVER['HTTP_HOST']);
update_option('home', 'http://' . $_SERVER['HTTP_HOST']);
```

wordpress will be running at `http://192.168.99.100:8080/`

## Team

| ![Ian Stewart ](https://avatars2.githubusercontent.com/u/14125280?v=3&s=150) | ![Joran Out ](https://avatars2.githubusercontent.com/u/9798364?v=3&s=150) |
| --- | --- |
| [Ian Stewart](https://github.com/IanCStewart) | [Joran Out](https://github.com/JoranOut) |
