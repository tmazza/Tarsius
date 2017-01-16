![Logo](web/protected/webroot/img/logo-small.png)

## Sobre
Processamento de folhas óticas de respostas. 

Realiza a interpretação das marcas e caracteres de uma imagem a partir das informações contidas em um template(arquivo [JSON](http://www.json.org/)), o qual especifica a posição, o tipo e saída esperada para cada região da imagem.

#### Funcionalidades

* [OMR](https://en.wikipedia.org/wiki/Optical_mark_recognition) de regiões elípticas
* [OCR](https://en.wikipedia.org/wiki/Optical_character_recognition) de caracteres numéricos
* Exportação dos resultados para base de dados ou via HTTP.
* Tolerância à transformações 2D (rotação, translação e escala) na imagem.
* Distribuição do processamento de um trabalho em vários núcleos

## Pré-requisitos da imagem

* resolução mínima de 200dpi (recomendado 300dpi)
* possuir quatro âncoras triangulares, dispostas formando um retângulo. 

[Exemplo de folha óptica de respostas](tarsius/tests/images/i1.jpg)



## Pré-requisitos de sistema

* sistema linux (testado no Ubuntu 14.04, no Ubuntu 16.04 e no CentOS 6.8)
* PHP >= 5.6, PHP 7 ou [HHVM](http://hhvm.com/)
* módulo [GD](https://secure.php.net/manual/pt_BR/book.image.php) ou [ImageMagick](http://php.net/manual/pt_BR/book.imagick.php)
* [Composer](https://getcomposer.org/) instalado 
* Servidor Web (para uso da interface), recomenda-se [Apache Server](https://httpd.apache.org/)
* Banco de dados relacional, compatíveis [SQLite](https://sqlite.org/), [MySQL](https://www.mysql.com/), [MariaDB](https://mariadb.org/), [PostgreSQL](https://www.postgresql.org/), SQL Server, Oracle



## Instalação

Baixe o repositório em uma pasta acessível pelo seu Servidor Web.

No caso do Apache o diretório padrão será /var/www/html, clone o repositório gerando o caminho /var/www/html/tarsius, desta forma ao final da instalação a aplicação ficará disponível em `http://localhost/tarsius`.


#### Configuração Base de dados

Crie uma base de dados para o Tarsius e importe a estrutura disponível em [web/protected/data/scheme.sql](web/protected/data/scheme.sql).


Configure o Tarsius para se conectar a base de dados criada. Para isso, edite o arquivo [web/protected/config/database.php](web/protected/config/database.php) de acordo com o banco de dados que será utilizado. 

Abaixo é mostrado como definir a conexão para MySQL, sendo que \<host>, \<database>, \<username>, \<password> devem ser trocados pelos valores que estabeleçam a conexão com o banco.


```php
<?php
return array(
    'class' => 'CDbConnection',
    'connectionString' => 'mysql:host=<host>;dbname=<database>',
    'username' => '<username>',
    'password' => '<password>',
    'emulatePrepare'=>true,  // necessário em algumas instalações do MySQL
);
```
Para definir a conexão para outros bancos basta alterar a string 'connectionString' com driver adequado, conforme:

* SQLite: sqlite:/path/to/dbfile
* MySQL/MariaDB: mysql:host=\<host>;dbname=\<database>
* PostgreSQL: pgsql:host=\<host>;port=5432;dbname=\<database>
* SQL Server: mssql:host=\<host>;dbname=\<database>
* Oracle: oci:dbname=//\<host>/\<database>

Mais informações em [Establishing Database Connection](http://www.yiiframework.com/doc/guide/1.1/en/database.dao#establishing-database-connection)

#### Configuração do Tarsius

È preciso gerar o arquivo autoload para as classes do Tarsius e montar a estrutura de diretórios que é esperada. Estando na raiz do repositório execute os comandos:

```
cd tarsius
composer install --no-dev
```

Sendo tarsius o diretório em [./tarsius](./tarsius).

## Uso

Ao final dos passos descritos acima a aplicação estará acessível em `http://localhost/tarsius`.

Próximas etapas (documentação em criação) 

* Ajustar valores das configurações padrões
* Geração de template
* Criação de uma trabalho
* Definir exportação dos resultados
  
Para mais informações, guias e tutoriais sobre o projeto acesse a [Wiki](https://github.com/ufrgs/tarsius/wiki/) do repositório.



