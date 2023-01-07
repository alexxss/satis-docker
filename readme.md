[Satis](https://composer.github.io/satis/) in Docker
---


This service provides:

- a frontend interface where users can view registered packages, and
- an endpoint that can trigger a full rebuild, or a partial rebuild for a specific package.

Frontend interface can be viewed at `127.0.0.1:FE_HTTP_PORT`, where `FE_HTTP_PORT` is the value you provide in `.devcontainer/.env`. See [Setup](#setup) below.

It is not of much use in a local environment, but can be convenient if deployed onto a server.
For example, you can set up a push-tag event GitHub Action for your package repositories to send a POST request to the endpoint with the package name as payload. A Satis partial rebuild will be triggered whenever a new package version is published.

See `src/index.php` for the endpoint's methods and parameters.

Setup
---


1.  Configure app .env

    - Copy `.devcontainer/.env.example` to `.devcontainer/.env`
    - Edit relevant variables in `.devcontainer/.env`
    - Optional: replace FPM_UID and FPM_GID in `.devcontainer/.env` 
        ```bash
        sed -i "s/FPM_UID=.*/FPM_UID=$(id -u)/;s/FPM_GID=.*/FPM_GID=$(id -g)/" .devcontainer/.env
        ```

1.  Setup
    1.  Spin up containers
        ```bash
        docker-compose up -d
        ```
    1.  Enter fpm container
        ```bash
        docker exec -it satis_fpm_1 bash
        ```
    1.  Setup composer credentials
        ```bash
        composer config github-oauth.github.com <your-token-here>
        ```
    1.  Use composer to install Satis
        ```bash
        composer install
        ```

Usage
---


### Interacting with Satis

`composer list satis` for available Satis commands.

Example:

- Init: `composer satis:init --name <company-name> --homepage <homepage-url>`
- Add a repository: `composer satis:add --type vcs --name <repository-full-name> <repository-url>`
- Rebuild a repository by name: `composer satis:build satis.json /build <repository-full-name>`
- Rebuild a repository by url: `composer satis:build --repository-url <repository-url> satis.json /build`
    
### File location

-   `satis.json`

    - In project: `src/satis.json`
    - In fpm container: `/app/satis.json`
    
-   Satis output directory

    - In project: `data/build`
    - In fpm container: `/build`
    - In nginx container: `/var/www/satis`
    
### Troubleshooting

#### `/build/include` does not exist and could not be created 

Run the following command in host:
```bash
sudo chown -R $(id -u):$(id -g) data/build
```

#### Cannot create cache directory `/composer/repo/...`, or directory is not writable. Proceeding without cache. See also cache-read-only config if your filesystem is read-only.

Run the following command in host:
```bash
sudo chown -R $(id -u):$(id -g) data/composer
```

#### Bad credentials

Make sure the token you provided to composer has permissions to read your private packages.

Use following command in `fpm` container to see which token is in use:
```bash
composer config -a github-oauth
```

Your token should be stored at `src/auth.json`. Use following command in `fpm` container to see where are your composer credentials stored:
```bash
composer config -a --source github-oauth
```

See [composer authentication](https://getcomposer.org/doc/articles/authentication-for-private-packages.md#command-line-github-oauth) for more information on how to supply credentials if your private packages are hosted on other platforms.
