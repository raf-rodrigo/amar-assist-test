# Gerenciador Financeiro

Este projeto é um sistema simples para controlar receitas, despesas e categorias.

Cada pessoa possui sua própria conta. Depois de entrar no sistema, ela só consegue visualizar e alterar os próprios dados.

## O que o sistema faz

- permite entrar com e-mail e senha;
- cadastra, edita, pesquisa e exclui categorias;
- cadastra, edita, pesquisa e exclui receitas;
- cadastra, edita, pesquisa e exclui despesas;
- mostra o total de receitas, despesas e o saldo do mês atual;
- exibe 20 registros por página;
- envia um aviso por e-mail sobre despesas que vencem no dia seguinte;
- impede que um usuário acesse os dados de outro usuário.

## Tecnologias utilizadas

- Laravel 9 e PHP 8.1 no backend;
- Vue 3 no frontend;
- TailwindCSS para o visual das telas;
- PostgreSQL para armazenar os dados;
- Redis para controlar os trabalhos executados em segundo plano;
- Laravel Sanctum para proteger a API;
- Laravel Sail e Docker para executar o projeto;
- PHPUnit para os testes automatizados.

## Antes de começar

É necessário ter o Docker instalado e funcionando.

Abra o terminal e entre na pasta do projeto:

```bash
cd /home/rafael-rodrigo/Documentos/Testes_Processos_Seletivo/amar_assist
```

### Primeira instalação

Se você acabou de baixar o projeto e a pasta `backend/vendor` ainda não existe, execute:

```bash
docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$PWD/backend:/var/www/html" \
  -w /var/www/html \
  laravelsail/php81-composer:latest \
  composer install --ignore-platform-reqs
```

Esse comando instala as dependências do Laravel dentro de um container. Não é necessário instalar PHP ou Composer diretamente no computador.

Depois, crie o arquivo de configuração:

```bash
cp backend/.env.example backend/.env
```

Informe ao Sail o usuário do seu computador para evitar problemas de permissão:

```bash
export WWWUSER=$(id -u)
export WWWGROUP=$(id -g)
```

## Criando um atalho para o Sail

Para não precisar digitar `./backend/vendor/bin/sail` em todos os comandos, você pode criar um alias chamado `sail`.

### Alias temporário

Execute este comando na pasta principal do projeto:

```bash
alias sail='./backend/vendor/bin/sail'
```

Agora, em vez de escrever:

```bash
./backend/vendor/bin/sail artisan test
```

você pode escrever apenas:

```bash
sail artisan test
```

Esse alias funciona enquanto o terminal estiver aberto. Como ele usa um caminho relativo, execute os comandos a partir desta pasta:

```text
/home/rafael-rodrigo/Documentos/Testes_Processos_Seletivo/amar_assist
```

### Alias permanente

Para disponibilizar o alias em novos terminais, adicione ao final do arquivo `~/.bashrc`:

```bash
alias sail='/home/rafael-rodrigo/Documentos/Testes_Processos_Seletivo/amar_assist/backend/vendor/bin/sail'
```

Depois, atualize o terminal:

```bash
source ~/.bashrc
```

Você poderá usar o comando `sail` em qualquer pasta:

```bash
sail up -d
sail artisan migrate --seed
sail artisan test
sail down
```

## Como iniciar o projeto

Construa e inicie os containers:

```bash
sail up -d --build
```

Crie a chave de segurança da aplicação:

```bash
sail artisan key:generate
```

Crie as tabelas e o usuário de demonstração:

```bash
sail artisan migrate --seed
```

Depois disso, acesse:

- Sistema: http://localhost:5173
- API: http://localhost:8080/api

## Usuários de demonstração

```text
Usuário Demonstração
E-mail: demo@example.com
Senha: password

Ana Souza
E-mail: ana@example.com
Senha: password

Bruno Lima
E-mail: bruno@example.com
Senha: password
```

Cada conta possui seus próprios dados. Ao entrar com um desses usuários, não é possível visualizar ou alterar os registros das outras contas.

O seeder cria somente os usuários de demonstração. As categorias devem ser cadastradas pelo próprio usuário na tela **Categorias** antes de criar uma receita ou despesa.

## Comandos do dia a dia

Ver quais containers estão funcionando:

```bash
sail ps
```

Executar os testes:

```bash
sail artisan test
```

Apagar os dados e criar novamente o banco de demonstração:

```bash
sail artisan migrate:fresh --seed
```

Testar manualmente a procura por despesas que vencem amanhã:

```bash
sail artisan expenses:dispatch-reminders
```

Ver o que a fila e o agendador estão fazendo:

```bash
sail logs -f queue scheduler
```

Parar o projeto:

```bash
sail down
```

Iniciar novamente:

```bash
sail up -d
```

## Para que serve cada container

- `laravel.test`: executa a API Laravel;
- `frontend`: executa as telas feitas com Vue;
- `postgres`: guarda usuários, categorias, receitas e despesas;
- `redis`: guarda temporariamente os trabalhos que precisam ser processados;
- `queue`: processa os trabalhos da fila, como o envio de e-mails;
- `scheduler`: verifica os horários das tarefas automáticas.

## Configuração de e-mail

Quando `MAIL_MAILER` não é informado, os e-mails são gravados no arquivo de log da aplicação. Dessa forma, é possível testar sem criar uma conta externa. Para usar o Mailtrap, defina `MAIL_MAILER=smtp` e preencha os dados abaixo.

Para enviar os e-mails ao ambiente de testes do Mailtrap, altere estas informações no arquivo `backend/.env`:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_usuario
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=tls
```

Todos os dias às 08:00, o sistema procura usuários com despesas que vencem no dia seguinte. Quando encontra, coloca o envio do aviso na fila. O container `queue` pega esse trabalho e envia o e-mail.

## Regras importantes

- Um usuário não pode acessar categorias, receitas ou despesas de outro usuário.
- O frontend nunca informa qual é o dono de um lançamento. O backend usa automaticamente o usuário que está conectado.
- Uma receita não pode ter uma data passada nem uma data posterior ao último dia do mês atual.
- Uma despesa não pode ter uma data passada nem uma data superior a 12 meses no futuro.
- Valores não podem ser negativos.
- Descrições podem ter no máximo 191 caracteres.
- Uma categoria só pode ser usada pelo usuário que a criou.
- Uma categoria que já está sendo usada não pode ser excluída.

## Como o saldo é calculado

O dashboard mostra os valores do mês atual:

```text
Saldo = total de receitas - total de despesas
```

As somas são feitas diretamente pelo PostgreSQL. Isso evita carregar todos os lançamentos apenas para somá-los e deixa a consulta mais eficiente.

## Endereços principais da API

```text
POST   /api/register       Criar uma conta
POST   /api/login          Entrar no sistema
POST   /api/logout         Sair do sistema
GET    /api/user           Consultar o usuário conectado
GET    /api/dashboard      Consultar os valores do mês

/api/categories            Categorias
/api/incomes               Receitas
/api/expenses              Despesas
```

As listagens aceitam os parâmetros `search` para pesquisa e `page` para mudança de página. Cada página possui no máximo 20 registros.

## Testes automatizados

Os testes verificam principalmente se:

- um usuário não consegue acessar dados de outro usuário;
- não é possível usar uma categoria de outra pessoa;
- as regras de datas são respeitadas;
- o dashboard usa apenas os dados do usuário conectado e do mês atual.

Para executar:

```bash
sail artisan test
```
