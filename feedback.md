# Feedback
Esse documento visa coletar feedbacks sobre o teste de desenvolvimento. Desde o início do teste até a entrega do projeto.

## Antes de Iniciar o Teste

1 - Fale sobre suas primeiras impressões do Teste:
> Achei a ideia do projeto diferente, então automaticamente achei interessente e decidi seguir com o desafio de inicio tive algumas dúvidas, mas depois de um tempo entendi como eu deveria fazer

2 - Tempo estimado para o teste:
> Acredito que devo levar 2 dias para entregar tudo funcionando como solicitado e com os testes

3 - Qual parte você no momento considera mais difícil?
> No momento considero mais difícil o passagem de parâmetros no /r/{redirect} ainda preciso pensar como isso vai funcionar

4 - Qual parte você no momento considera que levará mais tempo?
> Acho que vai ser nos testes, pois acho que tem bastante coisinha para testar e garantir que nada vai quebrar

5 - Por onde você pretende começar?
> Primeiro eu vou fazer um resumo de tudo que eu entendi e criar TODOs para cada tarefa, e depois pretendo começar criando as models e migrations 


## Após o Teste

1 - O que você achou do teste?
> Achei o teste bem desafiante, me propocionou algumas horas de estudo para entender como validar um DNS da forma certa, fora que tive que me permitir estruturar o código de uma forma que acredito que fique mais organizado.

2 - Levou mais ou menos tempo do que você esperava?
> Sim, levei mais tempo do que esperava

3 - Teve imprevistos? Quais?
> Tive mais dificuldade em montar os testes, faz um tempinho que não mexo com teste unitário, então bati a cabeça um pouco pra entender e conseguir seguir com os testes

4 - Existem pontos que você gostaria de ter melhorado?
> Sim, gostaria de ter feito um layout simples pra ficar algo mais amigável de usar, pensei em algumas coisas legais que poderia ter feito no front. E também se pudesse melhoraria um pouco mais na estrutura do código.

5 - Quais falhas você encontrou na estrutura do projeto?
> A falha principal para mim que me fez seguir o projeto para um caminho mais complicado, foi a sujestão de chamar uma model direto da controller, isso vai contra algumas das boas práticas e fica mais dificil de dar manutenção e testar, então por isso optei por usar o Services e as Facades, assim separei a lógica de negócio da lógica de código