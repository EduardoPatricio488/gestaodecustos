<?php

namespace Database\Seeders;

use App\Models\StoreBundle;
use App\Models\StoreCoupon;
use App\Models\StoreProduct;
use App\Services\StorePdfService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'title' => 'IA Financeira PRO',
                'type' => 'ia',
                'price' => 39.90,
                'image' => '🧠',
                'badge' => 'Mais Vendido',
                'is_featured' => true,
                'sales_count' => 320,
                'rating_avg' => 4.8,
                'rating_count' => 45,
                'points_reward' => 50,
                'description' => 'Previsões de saldo, anomalias, alertas automáticos e análise de padrões.',
                'features' => ['📈 Previsões de saldo', '🔔 Alertas automáticos', '🧠 Análise de padrões com IA'],
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'screenshots' => ['Dashboard com previsões', 'Alertas de anomalias', 'Gráficos de padrões'],
                'faq' => [
                    ['q' => 'Funciona com qualquer banco?', 'a' => 'Sim, compatível com importação manual e automática.'],
                    ['q' => 'Preciso de conhecimentos técnicos?', 'a' => 'Não, a IA faz tudo automaticamente.'],
                ],
                'roadmap' => [
                    ['date' => 'Q3 2026', 'title' => 'Previsões multi-conta', 'desc' => 'Agregar todas as contas numa previsão única'],
                    ['date' => 'Q4 2026', 'title' => 'Alertas por WhatsApp', 'desc' => 'Notificações em tempo real'],
                ],
                'related_products' => [4, 5],
            ],
            [
                'title' => 'IA Empresarial PRO',
                'type' => 'ia',
                'price' => 59.00,
                'image' => '🏢',
                'badge' => 'Business',
                'is_featured' => true,
                'sales_count' => 180,
                'rating_avg' => 4.7,
                'rating_count' => 28,
                'points_reward' => 75,
                'description' => 'Automatiza análise de custos, RH, salários e relatórios empresariais.',
                'features' => ['🏢 Análise de custos', '👥 Integração RH', '📊 Relatórios automáticos'],
                'related_products' => [6, 7],
            ],
            [
                'title' => 'IA Subscrições Inteligentes',
                'type' => 'ia',
                'price' => 24.90,
                'image' => '🔍',
                'badge' => 'Novo',
                'sales_count' => 95,
                'rating_avg' => 4.6,
                'rating_count' => 12,
                'points_reward' => 30,
                'description' => 'Deteta subscrições, calcula impacto anual e sugere otimizações automáticas.',
                'features' => ['🔍 Deteção de subscrições', '💰 Impacto anual', '✨ Sugestões de otimização'],
            ],
            [
                'title' => 'Widget Mercado Global PRO',
                'type' => 'widget',
                'price' => 19.90,
                'image' => '📈',
                'sales_count' => 210,
                'rating_avg' => 4.5,
                'rating_count' => 33,
                'points_reward' => 20,
                'description' => 'S&P500, NASDAQ, DAX, CAC40, FTSE100 com insights automáticos.',
                'features' => ['📈 Índices globais', '⚡ Dados em tempo real', '🧠 Insights automáticos'],
                'screenshots' => ['Widget no dashboard', 'Detalhe S&P500', 'Comparativo índices'],
            ],
            [
                'title' => 'Widget Energia & Commodities',
                'type' => 'widget',
                'price' => 19.90,
                'image' => '⚡',
                'sales_count' => 140,
                'description' => 'Petróleo, gás, metais preciosos e energia com dados em tempo real.',
                'features' => ['⚡ Energia', '🛢️ Commodities', '💎 Metais preciosos'],
            ],
            [
                'title' => 'Categorização Automática PRO',
                'type' => 'automation',
                'price' => 29.90,
                'image' => '⚙️',
                'sales_count' => 165,
                'description' => 'Classificação automática de despesas e receitas com IA.',
                'features' => ['⚙️ Classificação automática', '📥 Importação inteligente', '🎯 Regras personalizáveis'],
            ],
            [
                'title' => 'Dados Macroeconómicos PRO',
                'type' => 'data',
                'price' => 14.90,
                'image' => '🌍',
                'sales_count' => 88,
                'description' => 'Indicadores globais via World Bank Data360.',
                'features' => ['🌍 Indicadores globais', '📊 Data360', '📈 Tendências macro'],
            ],
            [
                'title' => 'Curso: Mestria em Finanças Pessoais',
                'type' => 'course',
                'price' => 49.90,
                'image' => '🎓',
                'badge' => 'Bestseller',
                'is_featured' => true,
                'sales_count' => 420,
                'rating_avg' => 4.9,
                'rating_count' => 67,
                'points_reward' => 100,
                'description' => 'Curso completo em PDF e vídeo: orçamento, poupança, investimento e liberdade financeira com 6 módulos práticos.',
                'long_content' => 'Programa estruturado para dominar finanças pessoais do zero ao investidor confiante. Cada módulo inclui vídeo YouTube, material visual, exercícios e PDF para download.',
                'features' => [
                    '🎬 6 módulos em vídeo (YouTube)',
                    '📄 PDF por módulo + workbook completo',
                    '🖼️ Infográficos e exemplos visuais',
                    '✅ Exercícios práticos em cada lição',
                ],
                'learning_outcomes' => [
                    'Criar e manter um orçamento mensal eficaz',
                    'Construir um fundo de emergência em 6 meses',
                    'Investir com confiança em ETFs e ações',
                    'Eliminar dívidas com o método avalanche',
                    'Planear objetivos financeiros a 1, 3 e 5 anos',
                    'Proteger o património com seguros essenciais',
                ],
                'video_url' => 'https://www.youtube.com/embed/GcGiYWmAbKo',
                'screenshots' => [
                    'Dashboard de orçamento mensal',
                    'Simulador de fundo de emergência',
                    'Alocação de carteira ETFs',
                ],
                'course_modules' => $this->financasPessoaisModules(),
                'faq' => [
                    ['q' => 'O curso é em PDF?', 'a' => 'Sim. Cada módulo tem PDF próprio e há um workbook completo para download.'],
                    ['q' => 'Os vídeos são incluídos?', 'a' => 'Sim, cada módulo tem vídeo YouTube integrado na plataforma.'],
                    ['q' => 'Tenho acesso vitalício?', 'a' => 'Sim, incluindo atualizações futuras dos PDFs.'],
                ],
            ],
            [
                'title' => 'Curso: Investimento e Mercados',
                'type' => 'course',
                'price' => 39.90,
                'image' => '📊',
                'badge' => 'Novo',
                'sales_count' => 156,
                'rating_avg' => 4.7,
                'rating_count' => 24,
                'points_reward' => 80,
                'description' => 'Aprende a analisar mercados, montar carteira e gerir risco com vídeos, imagens e PDFs por módulo.',
                'long_content' => 'Curso focado em investimento: desde conceitos básicos até estratégias de diversificação e análise de risco.',
                'features' => ['🎬 4 módulos em vídeo', '📄 PDFs descargáveis', '📈 Casos práticos reais'],
                'learning_outcomes' => [
                    'Compreender ações, obrigações e ETFs',
                    'Calcular risco e retorno esperado',
                    'Montar uma carteira diversificada',
                    'Rebalancear com disciplina',
                ],
                'video_url' => 'https://www.youtube.com/embed/zAtRt8EU6fg',
                'course_modules' => $this->investimentoModules(),
                'faq' => [
                    ['q' => 'Preciso de experiência prévia?', 'a' => 'Não, o curso começa do zero.'],
                ],
            ],
            [
                'title' => 'Guia: Otimização Fiscal',
                'type' => 'guide',
                'price' => 15.00,
                'image' => '📜',
                'sales_count' => 75,
                'rating_avg' => 4.6,
                'rating_count' => 18,
                'description' => 'Guia PDF completo sobre deduções, benefícios fiscais e planeamento legal para empresas e freelancers.',
                'features' => ['📜 PDF completo', '📖 Leitura online', '✅ Checklists fiscais'],
                'long_content' => $this->guideLongContent('fiscal'),
            ],
            [
                'title' => 'Guia: Fundo de Emergência em 90 Dias',
                'type' => 'guide',
                'price' => 9.90,
                'image' => '🛡️',
                'sales_count' => 112,
                'description' => 'Guia PDF passo a passo para criar o teu colchão financeiro em 3 meses.',
                'features' => ['📜 PDF prático', '📋 Plano semanal', '💡 Dicas de poupança'],
                'long_content' => $this->guideLongContent('emergency'),
            ],
            [
                'title' => 'Pack Gestão Business Pro',
                'type' => 'pack',
                'price' => 29.00,
                'image' => '💼',
                'badge' => 'Popular',
                'sales_count' => 155,
                'description' => 'Templates de P&L, dashboards de equipa e gestão de stock.',
                'features' => ['💼 Templates P&L', '📊 Dashboards de equipa', '📦 Gestão de stock'],
            ],
            [
                'title' => 'Starter',
                'type' => 'plan',
                'price' => 9.90,
                'image' => '🌱',
                'description' => 'Ideal para quem quer começar a automatizar finanças pessoais com IA básica.',
                'features' => ['IA Financeira básica', 'Orçamentos inteligentes', 'Alertas de limite'],
            ],
            [
                'title' => 'PRO',
                'type' => 'plan',
                'price' => 24.90,
                'image' => '⭐',
                'badge' => 'Recomendado',
                'description' => 'IA avançada, automação e dados macroeconómicos reais.',
                'features' => ['IA Financeira PRO', 'IA de Subscrições', 'Widgets premium', 'Dados macroeconómicos'],
            ],
            [
                'title' => 'Business',
                'type' => 'plan',
                'price' => 49.90,
                'image' => '🏢',
                'description' => 'Para empresas com RH, salários e relatórios avançados.',
                'features' => ['IA Empresarial PRO', 'Gestão de RH', 'Relatórios automáticos', 'Dados PRO'],
            ],
            [
                'title' => 'Template: Orçamento Familiar',
                'slug' => 'template-orcamento-familiar',
                'type' => 'guide',
                'price' => 7.90,
                'image' => '📋',
                'badge' => 'Novo',
                'sales_count' => 45,
                'description' => 'Planilha e guia PDF para orçamento familiar mensal com categorias pré-definidas.',
                'features' => ['📋 Template Excel/CSV', '📄 Guia PDF', '👨‍👩‍👧 Modo família integrado'],
                'download_path' => 'store/templates/orcamento-familiar.pdf',
            ],
            [
                'title' => 'Planilha: Controlo de Dívidas',
                'slug' => 'template-controlo-dividas',
                'type' => 'guide',
                'price' => 5.90,
                'image' => '📉',
                'sales_count' => 38,
                'description' => 'Planilha amortização avalanche/bola de neve + guia PDF.',
                'features' => ['📊 Planilha Excel', '📄 Método avalanche', '🎯 Simulador de prazos'],
                'download_path' => 'store/templates/controlo-dividas.pdf',
            ],
            [
                'title' => 'Pack Empreendedor',
                'slug' => 'pack-empreendedor',
                'type' => 'pack',
                'price' => 34.90,
                'image' => '🚀',
                'badge' => 'Pack',
                'is_featured' => true,
                'sales_count' => 62,
                'description' => 'Templates de faturação, P&L, propostas comerciais e curso rápido de gestão.',
                'features' => ['💼 Templates faturação', '📊 P&L empresarial', '📝 Propostas comerciais', '🎓 Mini-curso PDF'],
                'related_products' => [2, 6],
            ],
        ];

        $created = [];

        foreach ($products as $product) {
            $slug = $product['slug'] ?? Str::slug($product['title']);
            $created[$product['title']] = StoreProduct::updateOrCreate(
                ['slug' => $slug],
                array_merge($product, ['slug' => $slug])
            );
        }

        $pdf = app(StorePdfService::class);

        foreach ($created as $product) {
            if ($product->type === 'guide') {
                $sections = $this->guideSections($product->slug);
                $path = $pdf->guidePath($product->slug);
                $pdf->generateGuidePdf($product->title, $sections, $path);
                $product->update(['download_path' => $path]);
            }

            if ($product->type === 'course' && $product->course_modules) {
                $modules = $product->course_modules;
                foreach ($modules as $index => $module) {
                    $modulePath = $pdf->courseModulePath($product->slug, $module['number']);
                    $pdf->generateCourseModulePdf($product->title, $module, $modulePath);
                    $modules[$index]['pdf_path'] = $modulePath;
                }
                $workbookPath = $pdf->courseWorkbookPath($product->slug);
                $pdf->generateCourseWorkbookPdf($product->title, $modules, $workbookPath);
                $product->update([
                    'course_modules' => $modules,
                    'download_path' => $workbookPath,
                ]);
            }
        }

        StoreCoupon::updateOrCreate(['code' => 'PRO10'], [
            'type' => 'percent',
            'value' => 10,
            'min_purchase' => 20,
            'max_uses' => 100,
            'is_active' => true,
            'expires_at' => now()->addMonths(3),
        ]);

        StoreCoupon::updateOrCreate(['code' => 'WELCOME5'], [
            'type' => 'fixed',
            'value' => 5,
            'min_purchase' => 15,
            'is_active' => true,
        ]);

        $bundle = StoreBundle::updateOrCreate(['slug' => 'pack-ia-completo'], [
            'title' => 'Pack IA Completo',
            'description' => 'As 3 extensões de IA com desconto especial.',
            'price' => 99.90,
            'image' => '🧠',
            'badge' => 'Poupa 25%',
            'savings_percent' => 25,
            'is_active' => true,
        ]);

        $iaIds = collect($created)->filter(fn ($p) => $p->type === 'ia')->pluck('id');
        $bundle->products()->sync($iaIds);
    }

    private function financasPessoaisModules(): array
    {
        return [
            [
                'number' => 1,
                'title' => 'Mindset e Diagnóstico Financeiro',
                'description' => 'Avalia a tua situação atual, define prioridades e cria o mapa da tua vida financeira.',
                'duration' => '55 min',
                'video_url' => 'https://www.youtube.com/embed/GcGiYWmAbKo',
                'topics' => ['Património líquido', 'Rácio dívida/rendimento', 'Objetivos SMART'],
                'exercises' => ['Preenche a ficha de diagnóstico', 'Define 3 objetivos para 12 meses'],
                'images' => [
                    ['caption' => 'Mapa do património líquido', 'emoji' => '🗺️'],
                    ['caption' => 'Pirâmide das prioridades financeiras', 'emoji' => '🔺'],
                ],
            ],
            [
                'number' => 2,
                'title' => 'Orçamento que Funciona',
                'description' => 'Métodos 50/30/20, envelope digital e controlo de despesas fixas vs variáveis.',
                'duration' => '1h 10min',
                'video_url' => 'https://www.youtube.com/embed/ikM6eNY7-HU',
                'topics' => ['Rendimento líquido', 'Despesas fixas', 'Despesas variáveis', 'Categoria poupança'],
                'exercises' => ['Monta o teu orçamento no template PDF', 'Identifica 3 cortes possíveis'],
                'images' => [
                    ['caption' => 'Gráfico 50/30/20', 'emoji' => '📊'],
                    ['caption' => 'Calendário de despesas mensais', 'emoji' => '📅'],
                ],
            ],
            [
                'number' => 3,
                'title' => 'Fundo de Emergência',
                'description' => 'Quanto guardar, onde colocar e como automatizar transferências mensais.',
                'duration' => '45 min',
                'video_url' => 'https://www.youtube.com/embed/zAtRt8EU6fg',
                'topics' => ['3 a 6 meses de despesas', 'Contas remuneradas', 'Automatização'],
                'exercises' => ['Calcula o valor alvo do fundo', 'Configura transferência automática'],
                'images' => [
                    ['caption' => 'Simulador de fundo de emergência', 'emoji' => '🛡️'],
                ],
            ],
            [
                'number' => 4,
                'title' => 'Eliminar Dívidas',
                'description' => 'Método avalanche vs bola de neve, renegociação e priorização de juros.',
                'duration' => '50 min',
                'video_url' => 'https://www.youtube.com/embed/ujitMctuLkg',
                'topics' => ['TAEG', 'Amortização', 'Plano de ataque às dívidas'],
                'exercises' => ['Lista todas as dívidas por taxa de juro', 'Escolhe o método ideal'],
                'images' => [
                    ['caption' => 'Tabela comparativa avalanche vs neve', 'emoji' => '❄️'],
                ],
            ],
            [
                'number' => 5,
                'title' => 'Introdução ao Investimento',
                'description' => 'ETFs, diversificação, horizonte temporal e perfil de risco.',
                'duration' => '1h 20min',
                'video_url' => 'https://www.youtube.com/embed/LBfn5gL99Do',
                'topics' => ['Risco vs retorno', 'ETFs mundiais', 'DCA — investimento periódico'],
                'exercises' => ['Define o teu perfil de risco', 'Simula carteira 80/20'],
                'images' => [
                    ['caption' => 'Alocação de ativos por idade', 'emoji' => '📈'],
                    ['caption' => 'Evolução DCA vs lump sum', 'emoji' => '💹'],
                ],
            ],
            [
                'number' => 6,
                'title' => 'Plano Financeiro 5 Anos',
                'description' => 'Integra tudo num plano: objetivos, milestones, revisão trimestral.',
                'duration' => '40 min',
                'video_url' => 'https://www.youtube.com/embed/8aRKYWe5rRI',
                'topics' => ['Metas anuais', 'Revisão trimestral', 'Ajustes de rota'],
                'exercises' => ['Preenche o plano 5 anos no PDF', 'Agenda primeira revisão'],
                'images' => [
                    ['caption' => 'Timeline de objetivos financeiros', 'emoji' => '🎯'],
                ],
            ],
        ];
    }

    private function investimentoModules(): array
    {
        return [
            [
                'number' => 1,
                'title' => 'Como Funcionam os Mercados',
                'description' => 'Bolsa, índices, liquidez e o papel dos intermediários.',
                'duration' => '40 min',
                'video_url' => 'https://www.youtube.com/embed/LBfn5gL99Do',
                'topics' => ['Índices bolsistas', 'Spread bid-ask', 'Horários de mercado'],
                'exercises' => ['Pesquisa 3 ETFs de acumulação'],
                'images' => [['caption' => 'Principais índices mundiais', 'emoji' => '🌍']],
            ],
            [
                'number' => 2,
                'title' => 'Análise de Risco',
                'description' => 'Volatilidade, drawdown, correlação e tolerância ao risco.',
                'duration' => '50 min',
                'video_url' => 'https://www.youtube.com/embed/zAtRt8EU6fg',
                'topics' => ['Volatilidade anualizada', 'Máximo drawdown', 'Sharpe ratio simplificado'],
                'exercises' => ['Calcula drawdown de um ETF'],
                'images' => [['caption' => 'Gráfico de volatilidade histórica', 'emoji' => '📉']],
            ],
            [
                'number' => 3,
                'title' => 'Construir a Carteira',
                'description' => 'Core-satellite, rebalanceamento e custos.',
                'duration' => '55 min',
                'video_url' => 'https://www.youtube.com/embed/GcGiYWmAbKo',
                'topics' => ['ETF core global', 'Satélites temáticos', 'TER e impostos'],
                'exercises' => ['Define alocação target', 'Agenda rebalanceamento semestral'],
                'images' => [['caption' => 'Modelo core-satellite', 'emoji' => '🛰️']],
            ],
            [
                'number' => 4,
                'title' => 'Disciplina e Psicologia',
                'description' => 'Evitar FOMO, manter o plano em crises e registar decisões.',
                'duration' => '35 min',
                'video_url' => 'https://www.youtube.com/embed/8aRKYWe5rRI',
                'topics' => ['Viés de confirmação', 'Diário de investimento', 'Regras pessoais'],
                'exercises' => ['Escreve 5 regras de investimento'],
                'images' => [['caption' => 'Checklist antes de comprar', 'emoji' => '✅']],
            ],
        ];
    }

    private function guideSections(string $slug): array
    {
        return match ($slug) {
            'guia-fundo-de-emergencia-em-90-dias' => [
                [
                    'title' => 'Porquê 90 dias?',
                    'paragraphs' => ['Um fundo de emergência protege-te de imprevistos sem recorrer a crédito caro. Em 90 dias, com disciplina, consegues a primeira meta intermédia.'],
                    'bullets' => ['Meta inicial: 1 mês de despesas', 'Meta final: 3 a 6 meses'],
                ],
                [
                    'title' => 'Plano semanal',
                    'paragraphs' => ['Divide a meta pelo número de semanas e automatiza transferências à segunda-feira.'],
                    'bullets' => ['Semana 1-4: cortar subscrições', 'Semana 5-8: aumentar rendimento extra', 'Semana 9-12: consolidar hábito'],
                ],
            ],
            default => [
                [
                    'title' => 'Introdução à Otimização Fiscal',
                    'paragraphs' => ['A otimização fiscal legal permite reduzir a carga tributária através de deduções, benefícios e planeamento ao longo do ano.'],
                ],
                [
                    'title' => 'Deduções em IRS',
                    'paragraphs' => ['Conhece as categorias de despesas dedutíveis: saúde, educação, habitação, pensões e donativos.'],
                    'bullets' => ['Guarda todas as faturas com NIF', 'Usa e-fatura para validar despesas', 'Planeia gastos dedutíveis antes do fim do ano'],
                ],
                [
                    'title' => 'Empresa vs Trabalho Independente',
                    'paragraphs' => ['A estrutura jurídica impacta IRC, IVA e SS. Compara custos efetivos antes de formalizar.'],
                    'bullets' => ['Sociedade unipessoal', 'ENI com contabilidade organizada', 'Benefícios fiscais para PME'],
                ],
                [
                    'title' => 'Checklist anual',
                    'paragraphs' => ['Revisa o plano fiscal em janeiro e antes do fecho do exercício.'],
                    'bullets' => ['Reunião com contabilista', 'Simulação de cenários', 'Provisões para impostos'],
                ],
            ],
        };
    }

    private function guideLongContent(string $type): string
    {
        return match ($type) {
            'emergency' => "GUIA PDF — FUNDO DE EMERGÊNCIA EM 90 DIAS\n\n1. Calcula as tuas despesas essenciais mensais (renda, alimentação, transportes, utilities).\n\n2. Define a meta: começa com 1 mês de despesas em 30 dias, depois 2 meses em 60 dias, e 3 meses em 90 dias.\n\n3. Abre uma conta separada só para o fundo — evita misturar com despesas do dia a dia.\n\n4. Automatiza uma transferência semanal. Pequenos valores consistentes vencem esforços esporádicos.\n\n5. Corta 3 despesas na primeira semana: subscrições esquecidas, take-away em excesso, compras por impulso.\n\n6. Regista progresso todos os domingos. Celebrar milestones mantém a motivação.",
            default => "GUIA PDF — OTIMIZAÇÃO FISCAL\n\nEste guia explica como pagar menos impostos de forma legal em Portugal, quer sejas freelancer, empresário ou trabalhador por conta de outrem.\n\nCAPÍTULO 1 — Deduções em IRS\nAs despesas com NIF associado podem reduzir o imposto a pagar. Categorias principais: saúde, educação, habitação, lares, pensões, donativos e despesas gerais.\n\nCAPÍTULO 2 — Planeamento ao longo do ano\nNão deixes tudo para março. Planeia compras dedutíveis, contribuições para PPR e donativos antes do fecho do ano fiscal.\n\nCAPÍTULO 3 — Empresas e IRC\nPara PME, existem benefícios como taxas reduzidas de IRC em lucros reinvestidos. Mantém contabilidade organizada e provas de todas as despesas.\n\nCAPÍTULO 4 — IVA e dedutibilidade\nSepara despesas com IVA dedutível. Em atividade independente, um erro aqui custa caro.\n\nCAPÍTULO 5 — Checklist anual\n□ Reunião com contabilista em janeiro\n□ Simular IRS/IRC com dados provisórios\n□ Rever estrutura societária\n□ Arquivar faturas e recibos\n\nDescarrega o PDF completo após compra para versão formatada e checklists imprimíveis.",
        };
    }
}
