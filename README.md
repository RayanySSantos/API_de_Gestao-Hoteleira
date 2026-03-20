# API de Gestão Hoteleira

API REST desenvolvida em **Laravel** para gerenciamento de **hotéis, quartos, tarifas e reservas**, com importação de dados via **XML**, persistência em **MySQL** e testes automatizados com **PHPUnit**.

## Sobre o desafio

Este projeto foi desenvolvido como solução para um desafio técnico de vaga júnior, com foco em:

- organização de código
- boas práticas
- arquitetura em camadas
- funcionamento correto da API
- testes automatizados
- clareza na estrutura da aplicação

## Objetivo da aplicação

A aplicação foi construída para atender aos seguintes requisitos:

- importar os dados iniciais a partir de arquivos XML
- permitir o gerenciamento de quartos e reservas via API
- impedir reservas em períodos já ocupados
- organizar a lógica de negócio usando **Service Layer**
- validar o comportamento com testes unitários e de integração

## Tecnologias utilizadas

- **PHP 8.2+**
- **Laravel 12**
- **MySQL**
- **Eloquent ORM**
- **PHPUnit**
- **Postman**
- **XML**
- **REST API**

## Estrutura do projeto

A aplicação foi organizada para separar responsabilidades e facilitar manutenção.

### Principais diretórios

- `app/Http/Controllers/Api`
  Responsável por receber requisições HTTP e retornar respostas JSON.

- `app/Http/Requests`
  Responsável por validar os dados recebidos antes de entrarem na regra de negócio.

- `app/Services`
  Responsável pela lógica principal da aplicação, como importação dos XMLs e validação de disponibilidade.

- `app/Models`
  Responsável por representar as entidades do sistema e seus relacionamentos com o banco.

- `database/migrations`
  Responsável pela criação da estrutura do banco de dados.

- `routes`
  Responsável por definir as rotas da API.

- `tests`
  Responsável pelos testes unitários e de integração.

## Arquitetura aplicada

Foi utilizada uma arquitetura simples, mas organizada, baseada em separação de responsabilidades:

- **Controllers**: controlam a entrada e saída da requisição
- **Requests**: validam dados enviados pelo cliente
- **Services**: concentram a regra de negócio
- **Models**: representam as tabelas e relacionamentos
- **Migrations**: definem a estrutura do banco

Essa abordagem evita controllers inchados e torna a aplicação mais fácil de testar e manter.

## Modelos da aplicação

Os principais models criados foram:

- `Hotel`
- `Room`
- `Rate`
- `Reservation`
- `ReservationGuest`
- `ReservationPrice`

### Relacionamentos principais

- Um **hotel** possui vários **quartos**
- Um **hotel** possui várias **tarifas**
- Um **hotel** possui várias **reservas**
- Um **quarto** pertence a um **hotel**
- Uma **reserva** pertence a um **hotel**
- Uma **reserva** pertence a um **quarto**
- Uma **reserva** pode possuir vários **hóspedes**
- Uma **reserva** pode possuir vários **preços por data**

## Importação dos XMLs

Os dados iniciais são importados a partir dos arquivos:

- `database/hotels.xml`
- `database/rooms.xml`
- `database/rates.xml`
- `database/reservations.xml`

A importação é feita por uma classe de serviço responsável por:

- localizar os arquivos XML
- ler os dados
- interpretar atributos e conteúdos
- persistir as informações no banco
- evitar duplicação usando atualização controlada

## Regra de negócio principal

A regra mais importante da aplicação é a de disponibilidade:

> O sistema não deve permitir que um quarto seja reservado se ele já possuir uma reserva ativa no período solicitado.

### Exemplo

Se já existir uma reserva para o quarto no período:

- check-in: `2026-04-10`
- check-out: `2026-04-12`

E uma nova reserva for solicitada para:

- check-in: `2026-04-11`
- check-out: `2026-04-13`

A nova reserva deve ser bloqueada, pois há sobreposição de datas.

Essa validação foi implementada no service de reservas.

## Rotas da API

### Importação

- `POST /api/import`

### Hotéis

- `GET /api/hotels`

### Tarifas

- `GET /api/rates`

### Quartos

- `GET /api/rooms`
- `POST /api/rooms`
- `GET /api/rooms/{id}`
- `PUT /api/rooms/{id}`
- `PATCH /api/rooms/{id}`
- `DELETE /api/rooms/{id}`

### Disponibilidade

- `GET /api/rooms/{id}/availability?check_in=YYYY-MM-DD&check_out=YYYY-MM-DD`

### Reservas

- `GET /api/reservations`
- `POST /api/reservations`
- `GET /api/reservations/{id}`
- `PUT /api/reservations/{id}`
- `PATCH /api/reservations/{id}`
- `DELETE /api/reservations/{id}`

## Como executar o projeto

### 1. Instalar dependências

```bash
composer install
