# Hotel Isabel

## 1. Introduction

Poniendo a a funcionar el proyecto en local, se puede acceder a la aplicación en la siguiente dirección:

http://hotelisabel-old.test/

.- instalar dependencias

```bash
cd es/
composer update
```

.- crear base de datos
    check files/rengine-db.sql

.- configurar .htaccess

write an htaccess file that redirects to es/ folder

```bash
    RewriteEngine on
    RewriteRule ^$ /es/ [L]
```




### 1.1. Purpose