# RouteRocket-API

## Descrição
Teste realizado para a empresa Payt, onde o desafio foi desenvolver uma API que fizesse redirects para sites salvos na base cadastrado pelo usuário.

## O que foi usado
### Para desenvolvimento foi utilizado 🧰 :
- PHP 8.2
- Laravel
- MySql
  
### Bibliotecas 📖 :
 - HashIds (Sugerida pela Payt para gerar hashs através do ID)
 - Guzzle

### Para manter o código organizado e tentar reaproveitar o maximo do código, ultilizei:
- Conceitos do SOLID com enfase no "S" (Single Responsiblity Principle)
- Facade Design pattern em conjunto com o ServiceProvider, assim consigo providenciar um interface static
me dando mais facilidade em acessar as funcionalidas
- Services usei para separar o a lógica de código da "lógica de negócio", assim deixando a controller fazendo seu trabalho, que é "CONTROLAR" rs
assim também tiro a necessidade de instaciar a Model direto na controller.

## Funcionalidades desenvolvidas 👷:
Bom as funcionalidas segui as sugestões fornecidas no .README do repositório original, sendo elas:

#### CRUD:
| Método  | Rota                                  | Descrição                          |
|---------|---------------------------------------|------------------------------------|
| GET     | /api/redirects                        | Listar todas as redirects criadas  |
| POST    | /api/redirects                        | Criar novas redirects              |
| PUT     | /api/redirects/{redirect_code}        | Atualizar uma redirect             |
| DELETE  | /api/redirects/{redirect_code}        | Deletar uma redirect               |
| GET     | /api/redirects/{redirect_code}/stats  | Listar estatísticas referente aos 10 últimos dias         |
| GET     | /api/redirects/{redirect_code}/logs   | Listar logs de acessos referente a um redirect            |
| GET     | /r/{redirect_code}                    | Redirecionar para a url de destino usando o redirect_code |

### Redirecionamento funcionando 🚀
[Gravação de tela de 2024-02-05 18-34-11.webm](https://github.com/MatheusVCosta/payt/assets/38003078/36baec45-b28c-4743-aa13-376f9c0304c9)

### Como iniciar o projeto
Para iniciar o projeto:
- Clonar o projeto
- Criar um banco de dados chamado "route_rocket_db"
- Na pasta raiz do projeto, copio o arquivo .env.example e renomei para .env
- Abra o .env e altere as seguintes informaçoes (Normalmente só vai ser necessário alterar o DB_USERNAME e DB_PASSWORD)
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=route_rocket_db
    DB_USERNAME=
    DB_PASSWORD=
- Agora é preciso executar um comando: composer install
- Pronto, basta executar php artisan serve
- Acesse: 127.0.0.1:8000 para ver se está tudo ok

Bom é isso, basta embarcar no 🚀 e sair navegando hehehe



    
