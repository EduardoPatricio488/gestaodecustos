<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryField;
use Illuminate\Database\Seeder;

class FixedCategoryFieldsSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            'Carro' => [
                ['label' => 'Quilometragem',    'key' => 'km',               'type' => 'number'],
                ['label' => 'Urgência',          'key' => 'urgencia',         'type' => 'select',   'options' => ['Urgente','Preventiva','Rotina']],
                ['label' => 'Local',             'key' => 'local',            'type' => 'text'],
                ['label' => 'Consumo (L/100km)', 'key' => 'combustivel',      'type' => 'number'],
                ['label' => 'Litros Abastecidos','key' => 'litros',           'type' => 'number'],
                ['label' => 'Estado',            'key' => 'estado',           'type' => 'select',   'options' => ['Bom','Razoável','Crítico']],
                ['label' => 'Próxima Inspeção',  'key' => 'proxima_inspecao', 'type' => 'date'],
                ['label' => 'Oficina de Confiança','key' => 'oficina_confianca','type' => 'checkbox'],
            ],
            'Casa' => [
                ['label' => 'Entidade',          'key' => 'entidade',         'type' => 'text'],
                ['label' => 'Piso/Divisão',      'key' => 'piso',             'type' => 'text'],
                ['label' => 'Consumo (kWh/m³)',  'key' => 'consumo',          'type' => 'number'],
                ['label' => 'Valor Anterior',    'key' => 'valor_anterior',   'type' => 'number'],
                ['label' => 'Referência',        'key' => 'referencia',       'type' => 'text'],
                ['label' => 'Data de Vencimento','key' => 'data_vencimento',  'type' => 'date'],
                ['label' => 'Anomalia Reportada','key' => 'anomalia',         'type' => 'checkbox'],
            ],
            'Alimentação' => [
                ['label' => 'Nº de Pessoas',     'key' => 'pessoas',          'type' => 'number'],
                ['label' => 'Estabelecimento',   'key' => 'estabelecimento',  'type' => 'text'],
                ['label' => 'Tipo de Dieta',     'key' => 'dieta',            'type' => 'select',   'options' => ['Omnívora','Vegetariana','Vegana','Sem glúten','Outra']],
                ['label' => 'Frequência',        'key' => 'frequencia',       'type' => 'select',   'options' => ['Diária','Semanal','Ocasional']],
                ['label' => 'Orçamento/Pessoa',  'key' => 'orcamento_pessoa', 'type' => 'number'],
                ['label' => 'Opção Saudável',    'key' => 'saudavel',         'type' => 'checkbox'],
            ],
            'Saúde' => [
                ['label' => 'Profissional',      'key' => 'profissional',     'type' => 'text'],
                ['label' => 'Especialidade',     'key' => 'especialidade',    'type' => 'text'],
                ['label' => 'Cobertura Seguro',  'key' => 'cobertura_seguro', 'type' => 'select',   'options' => ['Não','Sim, Parcial','Sim, 100%']],
                ['label' => 'Urgência',          'key' => 'urgencia',         'type' => 'select',   'options' => ['Rotina','Urgente','Emergência']],
                ['label' => 'Com Prescrição',    'key' => 'prescricao',       'type' => 'checkbox'],
                ['label' => 'Próxima Consulta',  'key' => 'proxima_consulta', 'type' => 'date'],
            ],
            'Tecnologia' => [
                ['label' => 'Fornecedor',        'key' => 'fornecedor',       'type' => 'text'],
                ['label' => 'Produto/Serviço',   'key' => 'produtoServico',   'type' => 'text'],
                ['label' => 'Estado',            'key' => 'status',           'type' => 'select',   'options' => ['Ativo','Pausado','Cancelado']],
                ['label' => 'Duração',           'key' => 'duracao',          'type' => 'select',   'options' => ['Mensal','Anual','Único']],
                ['label' => 'ROI Esperado',      'key' => 'roi_esperado',     'type' => 'text'],
                ['label' => 'Renovação Automática','key' => 'renovacao_automatica','type' => 'checkbox'],
                ['label' => 'Próxima Renovação', 'key' => 'proxima_renovacao','type' => 'date'],
            ],
            'Educação' => [
                ['label' => 'Instituição',       'key' => 'instituicao',      'type' => 'text'],
                ['label' => 'Curso',             'key' => 'curso',            'type' => 'text'],
                ['label' => 'Nível',             'key' => 'nivel',            'type' => 'select',   'options' => ['Básico','Intermédio','Avançado','Profissional']],
                ['label' => 'Estado do Pagamento','key' => 'estado_pagamento','type' => 'select',   'options' => ['Pago','Pendente','Faseado']],
                ['label' => 'Beneficiário',      'key' => 'beneficiario',     'type' => 'text'],
                ['label' => 'Com Certificação',  'key' => 'certificacao',     'type' => 'checkbox'],
            ],
            'Empréstimos' => [
                ['label' => 'Credor',            'key' => 'credor',           'type' => 'text'],
                ['label' => 'Valor Inicial',     'key' => 'valor_inicial',    'type' => 'number'],
                ['label' => 'Saldo Atual',       'key' => 'saldo_atual',      'type' => 'number'],
                ['label' => 'Taxa de Juros (%)', 'key' => 'taxa_juros',       'type' => 'number'],
                ['label' => 'Data de Término',   'key' => 'data_termino',     'type' => 'date'],
                ['label' => 'Seguro Associado',  'key' => 'seguro_associado', 'type' => 'checkbox'],
            ],
            'Seguros' => [
                ['label' => 'Tipo de Seguro',    'key' => 'tipo_seguro',      'type' => 'text'],
                ['label' => 'Seguradora',        'key' => 'seguradora',       'type' => 'text'],
                ['label' => 'Nº de Apólice',     'key' => 'numero_apolice',   'type' => 'text'],
                ['label' => 'Valor de Cobertura','key' => 'cobertura_valor',  'type' => 'number'],
                ['label' => 'Franquia',          'key' => 'franquia',         'type' => 'number'],
                ['label' => 'Data de Renovação', 'key' => 'data_renovacao',   'type' => 'date'],
                ['label' => 'Estado',            'key' => 'estado',           'type' => 'select',   'options' => ['Ativa','Suspensa','Cancelada']],
            ],
        ];

        // Mapeamento slug → workspace (não existe workspace no seeder,
        // por isso só populamos as categorias que JÁ existem na BD)
        foreach ($definitions as $name => $fields) {
            // Atualiza TODAS as categorias com este nome (pode haver em vários workspaces)
            $categories = Category::where('name', $name)->get();

            foreach ($categories as $category) {
                // Só adiciona se ainda não tiver campos
                if ($category->fields()->count() > 0) {
                    continue;
                }

                foreach ($fields as $order => $fieldDef) {
                    CategoryField::create([
                        'category_id' => $category->id,
                        'label'       => $fieldDef['label'],
                        'key'         => $fieldDef['key'],
                        'type'        => $fieldDef['type'],
                        'options'     => $fieldDef['options'] ?? null,
                        'required'    => false,
                        'order'       => $order,
                    ]);
                }
            }
        }
    }
}
