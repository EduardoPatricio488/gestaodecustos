# Demo Login

## Objetivo

Permitir que um comprador veja o produto em funcionamento sem precisar configurar o sistema do zero.

## Credenciais reais (geradas por `php artisan db:seed --class=DemoSeeder`)

### Admin da Plataforma (acesso a /admin)

- Email: admin@financepro.com
- Password: password
- Role: admin (acesso total ao painel de administração)

### CEO / Dono de Negócio (workspace pessoal + empresarial)

- Email: eduardo@financepro.com
- Password: password
- Plano: business

### Membro de Equipa (para testar convites/permissões)

- Email: joao@financepro.com
- Password: password

## Dados de exemplo para apresentar

- Workspace pessoal ("Cofre do Eduardo") e workspace empresarial ("Tech Solutions SaaS")
- 3 categorias (Alimentação, Servidores, Marketing)
- 3 meses de receitas e despesas do workspace empresarial
- 1 cliente, 1 projeto, 1 tarefa e 1 documento de negócio
- 1 conta bancária empresarial com saldo de exemplo
- João como membro convidado do workspace empresarial

## Como preparar a demo

1. `php artisan migrate:fresh` (ambiente de demo/staging, nunca em produção com dados reais)
2. `php artisan db:seed --class=DemoSeeder`
3. Confirmar no terminal as credenciais impressas pelo seeder
4. Fazer login com `eduardo@financepro.com` e verificar dashboard, categorias e workspace empresarial
5. Confirmar que Stripe (planos/checkout) aparece corretamente com as chaves de teste

## Dica comercial

Se o comprador quiser ver o produto em live, use:

- Login demo
- Workspace pessoal para ver dashboard financeiro
- Workspace business para ver gestão empresarial
- Plano premium para demonstrar os módulos exclusivos
