# MySQL Elastic Migrator

## Sobre

Essa aplicação tem como objetivo facilitar a migração de dados do MySQL para o ElasticSearch. Softwares que possuam o MySQL como seu banco de dados principal, podem facilmente enviar seus dados para o ElasticSearch para poderem usufruir dos benefícios que esse banco não relacional tem a oferecer de performance.

O MySQL Elastic Migrator não leva em conta como você estruturou as tabelas dentro do MySQL, o que importa é somente a configuração da consulta que você fará para a extração dos dados.

**Esse arquivo especifica comandos que funcionarão em um sistema operacional Linux! Podem existir diferenças nos comandos caso sejam executados em outros sistemas.**

### Requisitos

Primeiramente, como a aplicação disponibiliza todo funcionamento através de containeres Docker, é preciso que no ambiente que o software será colocado tenha o [Docker](https://www.docker.com/) instalado. Para inicialização desses containers é utilizado [Docker Compose](https://docs.docker.com/compose/). Essa documentação não contempla informações detalhadas a respeito do funcionamento ou uso dessas tecnologias, necessitando para uma necessidade a consulta direta às documentações citadas.

Outro requisito é possuir tanto o banco MySQL que tem os dados base para a migração quanto o banco ElasticSearch que receberá os dados migrados. Os dois bancos de dados precisam ser acessíveis do ambiente no qual o MySQL Elastic Migrator será executado.

Atenção: Podem ser necessárias ajustes de configurações para que o container consiga ter acesso aos bancos de dados devido a rede. Nesse caso, pode ser necessário modificar o arquivos `docker-compose.yml` na raiz do projeto.

### ElasticCloud

Recomendo a criação do banco ElasticSearch através do [ElasticCloud](https://www.elastic.co/). É possível a utilização do serviço por 14 dias de teste, facilitando assim o uso sem a necessidade da criação de qualquer infraestrutura. Além disso, a ferramenta também disponibiliza recursos para visualização das informações do banco, realização de consulta e outras diversas funcionalidades que podem facilitar todo processo de verificação dos dados e da utilidade dessa tecnologia nos diversos contextos da sua própria aplicação.

## Utilizando o MySQL Elastic Migrator

### Download da aplicação

Faça o download da aplicação através do git:

```
git clone https://github.com/wellingtondessuy/mysql-elastic-migrator.git
```

### Inicialização da aplicação

Primeiramente, crie um arquivo `.env`:

```
cp .env.example .env
```

#### Observação: há um serviço dentro do `docker-compose.yml` chamado `mysql-test`. Caso desejar um banco de dados MySQL para testar a ferramenta, você pode descomentar esse serviço para subir juntamente com a aplicação esse banco de dados.

Para a inicialização da aplicação é preciso criar e executar os containers. Faça isso executando o seguinte comando:

```
docker compose up -d
```

Verifique se os três containeres referentes a aplicação iniciaram corretamente: `migrator_nginx`, `migrator_app` e `migrator_mysql`. Com os containers iniciados é possível acessar a interface de configuração e execução da aplicação pelo navegador:

[http://localhost:8000](http://localhost:8000)

A seguinte tela deve estar disponível:

![Tela Inicial da Aplicação](https://github.com/wellingtondessuy/mysql-elastic-migrator/blob/master/docs/tela_inicial.png?raw=true)

### Configurações para funcionamento da aplicação

Primeiro necessidade é a configuração dos bancos que serão utilizados no processo de migração. Para configuração do banco MySQL, do qual serão consultados os dados que devem ser migrados, acesse o menu **Settings > MySQL** e preencha todas configurações:

[Configuração do Mysql](http://localhost:8000/mysql-page)

![Tela de Configuração do MySQL](https://github.com/wellingtondessuy/mysql-elastic-migrator/blob/master/docs/tela_config_mysql.png?raw=true)

Após isso, realize a configuração do ElasticSearch, para o qual os dados serão migrados. Para isso, acesse o menu **Settings > ElasticSearch** e preencha todas configurações:

[Configuração do ElasticSearch](http://localhost:8000/elastic-search-page)

![Tela de Configuração do ElasticSearch](https://github.com/wellingtondessuy/mysql-elastic-migrator/blob/master/docs/tela_config_elasticsearch.png?raw=true)

Há também um tela para configurações gerais da aplicação. Atualmente, existe somente uma configuração que é relativa a quantidade de registros a serem migrados a cada iteração do processamento. Caso queira alterar essa configuração, acesse o menu **Settings > General** e preencha todas configurações:

[Configurações Gerais](http://localhost:8000/general-page)

![Tela de Configurações Gerais](https://github.com/wellingtondessuy/mysql-elastic-migrator/blob/master/docs/tela_config_general.png?raw=true)

Essa quantidade possui valor padrão de 10000 e pode ser alterado para incrementar a performance da migração. No entanto, deve-se cuidar pois valores elevados podem causar problemas relacionados ao uso excessivo de memória ram.

Por fim, é necessária a configuração específicas dos dados que devem ser migrados pela aplicação. Para isso acesse o menu **Queries**. Você acessará a listagem de todas as consultas já cadastradas.

[Listagem de consultas cadastradas](http://localhost:8000/queries)

Através do botão **New Query** você pode cadastrar uma nova consulta de dados e configurá-la para a migração.

![Tela de Cadastro de Consulta](https://github.com/wellingtondessuy/mysql-elastic-migrator/blob/master/docs/tela_nova_consulta.png?raw=true)

Informações descritivas a respeito dos campos desse cadastro:

**ElasticSearch Index Name**: campo obrigatório de preenchimento. Deve conter uma string com nome do índice que receberá os dados no ElasticSearch. **Caso esse índice não exista no ElasticSearch, a aplicação irá criá-lo.** Não deve conter espaços ou caracteres especiais! Exemplos de nome: *sales*, *clients*, *customers_sales*... 

**Query**: campo obrigatório de preenchimento. Deve conter uma string com a consulta que deve ser realizada para busca dos dados no MySQL. **Deve ser um `SELECT` e não possuir os comandos `LIMIT` e `OFFSET` pois são utilizados pela própria aplicação para controle durante o processo de migração. Não utilizar `;` ao final da consulta.** Exemplos de consulta:

```
# Podem ser consultas simples e sem definição dos campos
SELECT * FROM sales

# Também podem ser consultas com joins e operações realizadas através dos campos
SELECT
	c.`id` as customer_id, 
	c.`name` as customer_name,
	c.`group` as customer_group,
	s.id as sale_id,
	s.`date` as sale_date,
	sp.id,
	sp.product_id,
	sp.quantity,
	sp.unit_value,
	(sp.quantity * sp.unit_value) as total_value,
	p.name as product_name,
	p.category_id as product_category_id,
	pc.title as product_category_title
FROM sale_products sp
JOIN sales s ON s.id = sp.sale_id
JOIN products p ON p.id = sp.product_id
JOIN product_categories pc ON pc.id = p.category_id 
JOIN customers c ON c.id = s.customer_id;
```

**Documents Unique Identifier**: campo opcional de preenchimento. Deve conter uma string com o campo que servirá de chave única para os documentos criados no ElasticSerach. Esse campo é importante para o caso de necessidade de atualização dos registros. Caso esse campo não seja preenchido, o próprio ElasticSearch criará uma chave para os documentos. Exemplo de campo: *id*. **Esse campo precisa estar contido no retorno da consulta configurado no campo Query.**

Todas as consultas cadastradas serão utilizadas durante o processo de migração, sendo consultados e migrados os dados em blocos até que chegue ao final de todos os registros retornados por essas consultas.

**Nota**: o container `migrator_mysql` não possui volume para persistência dos dados, então tudo que for configurado para funcionamento da aplicação está dentro desse container. Caso ele seja removido, as configurações serão perdidas.

### Execução da aplicação

Após todas as configurações acima serem realizadas, basta iniciar o processo de migração. Acesse o menu **Migrator - Execution**:

[Tela de Execução](http://localhost:8000/execution-page)

![Tela de Execução](https://github.com/wellingtondessuy/mysql-elastic-migrator/blob/master/docs/tela_execucao.png?raw=true)

Clique no botão **Start Migration**. Após isso, um log aparecerá e irá sendo atualizado conforme o processo de migração for sendo executado:

![Tela de Execução com Log](https://github.com/wellingtondessuy/mysql-elastic-migrator/blob/master/docs/tela_execucao_log.png?raw=true)

Para cada consulta cadastrada, ao encerrar a migração de todos os registros é exibido no log a seguinte mensagem:

```
[2025-11-13 00:36:24] local.INFO: All data migrated to ElasticSearch  
[2025-11-13 00:36:24] local.INFO: Query 1 has all data migrated to index sales at ElasticSearch! 
```

Acompanhe o log até que apareça essa mensagem para a última consulta que foi cadastrada. Você pode acompanhar pelo número do índice da consulta que exibirá no log. Considerando um cenário em que há 10 consultas cadastradas, a mensagem final será:

```
[2025-11-13 00:36:24] local.INFO: All data migrated to ElasticSearch  
[2025-11-13 00:36:24] local.INFO: Query 10 has all data migrated to index sales at ElasticSearch! 
```

![Tela de Execução com Log Final](https://github.com/wellingtondessuy/mysql-elastic-migrator/blob/master/docs/tela_execucao_log_2.png?raw=true)

Após isso, todos os dados foram migrados para o ElasticSearch e já estão disponíveis para consulta.

### Informações adicionais

O processo de migração pode ser executado mais de uma vez, no entanto, a aplicação de migração não realiza a limpeza dos dados no ElasticSearch. Nas consultas em que houver um **Documents Unique Identifier** cadastrado, os registros serão atualizados. Porém, se não houver serão criados novos registros juntamente com os que já estão no mesmo índice no ElasticSearch.

### ElasticCloud

Recomendo a criação do banco ElasticSearch através do [ElasticCloud](https://www.elastic.co/). É possível a utilização do serviço por 14 dias de teste, facilitando assim o uso sem a necessidade da criação de qualquer infraestrutura. Além disso, a ferramenta também disponibiliza recursos para visualização das informações do banco, realização de consulta e outras diversas funcionalidades que podem facilitar todo processo de verificação dos dados e da utilidade dessa tecnologia nos diversos contextos da sua própria aplicação.

### Geração de dados para validação da aplicação

**Atenção: Não execute esse processo de geração de dados se você estiver com o banco real da sua aplicação configurado.**

Como mencionado no tópico [Inicialização da Aplicação](#InicializaçãoDaAplicação), há um serviço comentado no `docker-compose.yml` chamado `mysql-test` que pode ser utilizado para validação. 

Caso utilize esse serviço, você deve configurar o banco MySQL da seguinte forma nas configurações da aplicação:

**Host**: `mysql-test`<br>
**Port**: `3306`<br>
**Database**: `mysql-elastic-migrator-test`<br>
**Username**: `elastic_migrator_user_test`<br>
**Password**: `migrator_pass`<br>

Dentro da aplicação há um menu que pode ser habilitado através da configuração `GENERATE_DATA_PAGE_ENABLED` presente no arquivo `.env`. Colocando o valor `true` nessa configuração um menu **Migrator - Generate Data** será habilitado. 

![Tela de Geração de Dados](https://github.com/wellingtondessuy/mysql-elastic-migrator/blob/master/docs/tela_generate_data.png?raw=true)

Essa geração criará dados no mesmo banco que está configurado na tela de configurações do MySQL. É necessário criar as tabelas no banco de teste através dos comandos existentes no arquivo `testing_database/schema.sql`. Toda vez que a geração de dados é executada, as tabelas que são manipuladas serão totalmente limpas e novos dados serão inseridos. Seguem as tabelas que são utilizadas: *customers*, *product_categories*, *products*, *sales* e *sale_products*.

Com esse banco é possível testar a criação de consultas e o processo de migração para ElasticSearch.