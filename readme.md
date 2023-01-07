1.  Configure app params

    - Copy `.env.example` to `.env`
    - Edit relevant variables in `.env`

1.  Configure composer credentials

    - Copy `data/composer/auth.json.example` to `data/composer/auth.json`
    - Edit `data/composer/auth.json` and add github (or other necessary) credentials in the line
    ```
    "github.com": "<your-token-here>" 
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
    1.  Use composer to install code
        ```bash
        composer install
        ```
    1. Set necessary file permissions
       ```bash
       chown -R daemon:daemon /build /composer
       ```

Usage
---


### Interacting with satis 

If your host environment does not have `daemon` user, use `--user 1:1`

```bash
docker exec -it --user $(id daemon -u):$(id daemon -g) satis_fpm_1 bash
```
    
`composer list satis` for available satis commands.

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
sudo chown -R 1:1 data/build
```

#### Cannot create cache directory `/composer/repo/...`, or directory is not writable. Proceeding without cache. See also cache-read-only config if your filesystem is read-only.

Run the following command in host:
```bash
sudo chown -R 1:1 data/composer
```