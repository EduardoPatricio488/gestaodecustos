# 🏦 Finance Pro - Premium Personal & Business ERP 💎

[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3-4e5ee4?style=for-the-badge&logo=livewire)](https://livewire.laravel.com)
[![TailwindCSS](https://img.shields.io/badge/Tailwind-3.0-38B2AC?style=for-the-badge&logo=tailwind-css)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-Commercial-gold?style=for-the-badge)](https://github.com/)

**Finance Pro** é um ecossistema SaaS de alta performance desenvolvido para unificar a disciplina financeira pessoal com a gestão empresarial avançada. Foi desenhado para oferecer uma experiência de "Centro de Comando" para indivíduos e CEOs de pequenas empresas.

---

## ✨ Funcionalidades em Destaque

### 👤 Gestão Pessoal (Cofre Privado)
*   **Experiência Gamificada:** Sistema de XP e Níveis para incentivar a disciplina financeira.
*   **CFO Inteligente (IA):** Insights em tempo real sobre hábitos de consumo alimentados por Gemini 2.0.
*   **Vault Blindado:** Espaço 100% privado e invisível para membros de outros workspaces.
*   **Gestor de Assinaturas:** Monitorização visual de custos recorrentes e renovações.
*   **Dropbox Familiar:** Arquivo digital seguro para documentos sensíveis (IDs, Contratos).

### 💼 ERP Empresarial (Modo CEO)
*   **Dashboard Executivo:** P&L em tempo real, contas a receber e provisões fiscais automáticas.
*   **Inteligência de Projetos:** Monitorização de margens de lucro e rentabilidade por projeto.
*   **Colaborador Shadow Mode:** Permite ao CEO visualizar o terminal como se fosse um colaborador para suporte.
*   **Controlo de Inventário:** Alertas inteligentes para níveis críticos de stock.
*   **Faturação Digital:** Sistema profissional de faturação integrado com Stripe.

---

## 🛠 Stack Tecnológica

*   **Framework:** Laravel 11 & PHP 8.3+
*   **Frontend:** Livewire 3 (SPA Experience) & Alpine.js
*   **Design System:** Flux UI (Interface limpa, moderna e minimalista)
*   **Base de Dados:** MySQL / PostgreSQL / SQLite
*   **Pagamentos:** Stripe (Laravel Cashier)
*   **Inteligência Artificial:** Integração via OpenRouter (Gemini / GPT-4)
*   **Mobile:** Suporte completo a **PWA** (Instalável em iOS e Android)

---

## 📦 Guia de Instalação Rápida

Siga estes passos para ter o ambiente local a funcionar em minutos:

### 1. Requisitos
*   PHP 8.3 ou superior
*   Composer
*   Node.js & NPM

### 2. Configuração do Projeto
```bash
# Clonar o repositório
git clone https://github.com/EduardoPatricio488/gestaodecustos.git
cd gestaodecustos

# Instalar dependências do PHP
composer install

# Instalar dependências de JS e compilar assets
npm install && npm run build

# Configurar Ambiente
cp .env.example .env
php artisan key:generate

3. Base de Dados e Dados de Demo
code Bash

# Correr as migrações
php artisan migrate

# Popular com dados profissionais de demonstração (Essencial para testes)
php artisan db:seed --class=DemoSeeder

4. Integração Stripe (Testes Locais)
code Bash

# Num terminal separado, inicie o listener de webhooks
stripe listen --forward-to localhost:8000/stripe/webhook

Nota: Copie o whsec_... gerado para a variável STRIPE_WEBHOOK_SECRET no seu ficheiro .env.
👤 Acesso de Demonstração

Após rodar o seeder, pode entrar com a conta de teste:

    Utilizador: eduardo@financepro.com

    Password: password

💸 Oportunidade Comercial

Este projeto está pronto para ser lançado como um modelo de negócio SaaS. Com o sistema de subscrições Stripe já integrado e a separação clara entre planos Free, Plus e Pro, o comprador pode começar a monetizar a plataforma imediatamente após o deploy.
🛡️ Licença

Este é um script comercial. Todos os direitos reservados.

Desenvolvido com precisão para quem leva a gestão financeira a sério.
