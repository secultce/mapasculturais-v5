# Mapas Culturais

Em julho de 2013, agentes culturais de vários países da América Latina e do Brasil se reuniram para discutir a criação de uma ferramenta de mapeamento de iniciativas culturais e gestão cultural. Desse encontro surgiram as bases para a criação de Mapas Culturais, um software livre que permite o aprimoramento da gestão cultural dos municípios e estados.

Mapas Culturais é uma plataforma colaborativa que reúne informações sobre agentes, espaços, eventos e projetos culturais, fornecendo ao poder público uma radiografia da área de cultura e ao cidadão um mapa de espaços e eventos culturais da região. A plataforma está alinhada ao Sistema Nacional de Informação e Indicadores Culturais do Ministério da Cultura (SNIIC) e contribui para a realização de alguns dos objetivos do Plano Nacional de Cultura.

A plataforma já está em uso em diversos municipios, estados, no governo federal em diversos projetos do ministério da cultura e até mesmo fora do Brasil no Uruguai. Instalações recentes: 


## Sobre a aplicação
Mapas Culturais é uma aplicação web server-side baseada em linguagem PHP e banco de dados Postgres, entre outras tecnologias e componentes, que propicia um ambiente virtual para mapeamento, divulgação e gestão de ativos culturais. 

## Projetos correlatos
* [Multiple Local Auth](https://github.com/mapasculturais/mapasculturais-MultipleLocalAuth) - Plugin de autenticação local + oauth.

## Instalação
A maneira mais simples e segura para instalar o Mapas Culturais é utilizando o [Mapa Cultural do Ceará](https://github.com/secultce/mapasculturais-v5) como base para a criação de um repositório próprio para o seu projeto, que reunirá o tema, os plugins e as configurações da aplicação. O Mapas Culturais Base Project utiliza o Docker e o Docker Composer para rodar a aplicação, facilitando os processos de deploy e de atualizaçao.

-
## Documentação


## Documentação Legada

A documentação pode ser navegada no endereço (http://docs.mapasculturais.org)



### [Software] Requisitos para Instalação
Lista dos principais softwares que compõe e aplicação. Maiores detalhes, ver documentação de [instalação](documentation/docs/mc_deploy.md) ou [guia do desenvolvedor](documentation/docs/mc_developer_guide.md). 

- [Ubuntu Server >= 18.04](http://www.ubuntu.com) ou [Debian Server >= 10](https://www.debian.org.)
- [PHP = 7.2](http://php.net)
  - [php-gd](http://php.net/manual/pt_BR/book.image.php)
  - [php-cli](https://packages.debian.org/pt-br/jessie/php5-cli)
  - [php-json](http://php.net/manual/pt_BR/book.json.php)
  - [php-curl](http://php.net/manual/pt_BR/book.curl.php)
  - [php-pgsql](http://php.net/manual/pt_BR/book.pgsql.php)
  - [php-apc](http://php.net/manual/pt_BR/book.apc.php)
- [Composer](https://getcomposer.org/)
- [PostgreSQL >= 10](http://www.postgresql.org/)
- [Postgis >= 2.2](http://postgis.net)
- [Node.JS >= 8.x](https://nodejs.org/en/)
  - [NPM](https://www.npmjs.com/)
  - [Terser](https://terser.org/)
  - [UglifyCSS](https://www.npmjs.com/package/gulp-uglifycss)
- [Ruby](https://www.ruby-lang.org/pt)
  - [Sass gem](https://rubygems.org/gems/sass/versions/3.4.22)

### [Hardware] Requisitos para instalação

Para instalações de pequeno/medio porte nas quais o número de entidades, isto é, número de agentes, espaços, projetos e evento,giram em torno de 2000 ativos, recomenda-se o mínimo de recursos para um servidor (aplicação + base de dados):

* 2 cores de CPU;
* 2gb de RAM;
* 50mbit de rede;

Desejável:

*  4 cores de CPU;
* 4gb de RAM;
* 100mbit de rede;

Para instalações em cidades de grande porte onde o número de entidades, isto é, número de agentes, espaços, projetos e evento, giram em torno de dezenas de milhares de ativos de cada, recomenda-se o mínimo de recursos para um servidor:

* 3 cores de CPU
* 3 gb de RAM
* 100mbit de rede

Recomendado:
* 6 cores de CPU
* 6 gb de RAM
* 500mbit de rede

Vale lembrar que os requisitos de hardware podem variar de acordo com a latência da rede, velocidade dos cores dos cpus, uso de proxies, entre outros fatores. Recomendamos aos sysadmin da rede em que a aplicação será instalada um monitoramento de tráfego e uso durante o período de 6 meses a 1 ano para avaliação de cenário de uso. 

### Canais de comunicação

### Licença de uso e desenvolvimento

Mapas Culturais é um software livre licenciado com [GPLv3](http://gplv3.fsf.org). 

