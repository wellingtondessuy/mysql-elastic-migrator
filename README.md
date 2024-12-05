# MySQL Elastic Migrator

## Sobre

Essa aplicação tem como objetivo facilitar a migração de dados do MySQL para o ElasticSearch. Softwares que possuam o MySQL como seu banco de dados principal, podem facilmente enviar seus dados para o ElasticSearch para poderem usufruir dos benefícios que esse banco não relacional tem a oferecer de performance.

O MySQL Elastic Migrator não leva em conta como você estruturou as tabelas dentro do MySQL, o que importa é somente a configuração da consulta que você fará para a extração dos dados.

**Esse arquivo especifica comandos que funcionarão em um sistema operacional Linux! Podem existir diferenças nos comandos caso sejam executados em outros sistemas.**

### Requisitos

Primeiramente, como a aplicação disponibiliza todo funcionamento através de um container Docker, é preciso que no ambiente que o software será colocado tenha o [Docker](https://www.docker.com/) instalado.

Outro requisito é possuir tanto o banco MySQL que tem os dados base para a migração quanto o banco ElasticSearch que receberá os dados migrados. Os dois bancos de dados precisam ser acessíveis do ambiente no qual o MySQL Elastic Migrator será executado.

## Utilizando o MySQL Elastic Migrator

### Download da aplicação

Faça o download da aplicação através do git:

```
git clone PREENCHER
```

### Configurações básicas

Execute os seguintes comandos para criar os arquivos de configuração:

```
cp .env.example .env
cp queries.json.example queries.json
```

No arquivo `.env` você deve preencher as informações de conexões para os dois bancos de dados

```
# Informações de conexão com o MySQL no qual os dados serão buscados
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

# Informações de conexão com o ElasticSearch para o qual os dados serão migrados
ELASTICSEARCH_HOST=
ELASTICSEARCH_API_KEY=
```

O arquivo `queries.json` deve conter as consultas que serão realizadas no banco MySQL para extrair os dados que irão compor cada documento que será enviado para o ElasticSearch no índice especificado. Algumas regras desse arquivo:

- Esse arquivo deve possuir um JSON
- O JSON deve possuir uma chave `queries` que é um array de objetos
- Cada objeto do array `queries` deve possuir os atributos:
    - index: string com nome do índice que receberá os dados no ElasticSearch
    - query: consulta que será realizada para buscar os dados no MySQL. Deve ser um `SELECT` e não pode possuir os comandos `LIMIT` ou `OFFSET`
    - document_identifier (Opcional): esse campo deve ser utilizado para que os documentos no ElasticSearch tenham o id conforme o conteúdo desse campo. Caso não seja utilizado, os id's serão gerados automaticamente pelo ElasticSearch. O campo aqui identificado deve ser retornado na query;

##### Opcional

Também no arquivo `.env`, você pode editar a quantidade de registros que a aplicação processa em cada iteração:

```
# Quantidade de registros a serem processados em cada busca (pode ser ajustado para melhor performance)
ROW_PER_ITERATION=10000
```

Um valor muito alto pode causar problemas de limitação memória.

### Build da imagem Docker

Construa a imagem que será usada como base para a criação do container. Faça isso através do seguinte comando:

```
docker build -t mysql-elastic-migrator-image .
```

### Executando a migração

Com o seguinte comando, crie e execute o container que já começará a migrar os dados conforme as definições do `queries.json`:

```
docker run -d -v /home/wellington/projects/mysql-elastic-migrator/:/usr/src/app --name mysql-elastic-migrator mysql-elastic-migrator-image
```

Para acompanhar o processo de migração através do log, execute o comando:

```
docker exec -it mysql-elastic-migrator tail -f storage/logs/laravel.log
```
