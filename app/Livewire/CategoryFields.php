<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\CategoryField;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class CategoryFields extends Component
{
    public Category $category;
    public $editingFieldId = null; // Controla se estamos a criar ou editar
    public $label, $type = 'text', $optionsRaw, $placeholder;
    public bool $required = false;

    protected $rules = [
        'label' => 'required|string|max:50',
        'type' => 'required|in:text,number,select,date,checkbox',
        'required' => 'boolean',
        'placeholder' => 'nullable|string|max:100',
    ];

    public function mount(Category $category)
    {
        if ($category->workspace_id !== auth()->user()->current_workspace_id) {
            abort(403);
        }
        $this->category = $category;
    }

    // --- CARREGAR DADOS PARA EDIÇÃO ---
    public function editField($id)
    {
        $field = $this->category->fields()->findOrFail($id);

        $this->editingFieldId = $field->id;
        $this->label = $field->label;
        $this->type = $field->type;
        $this->placeholder = $field->placeholder;
        $this->required = $field->required;
        $this->optionsRaw = is_array($field->options) ? implode(', ', $field->options) : '';

        // Faz scroll suave até ao formulário no topo (opcional, via browser)
        $this->dispatch('scroll-to-top');
    }

    // --- CANCELAR EDIÇÃO ---
    public function cancelEdit()
    {
        $this->reset(['label', 'type', 'optionsRaw', 'required', 'placeholder', 'editingFieldId']);
    }

    // --- SALVAR (CRIAR OU ATUALIZAR) ---
    public function saveField()
    {
        $this->validate();

        $data = [
            'label' => $this->label,
            'key' => str()->snake($this->label),
            'type' => $this->type,
            'required' => $this->required,
            'placeholder' => $this->placeholder,
            'options' => $this->type === 'select' ? array_map('trim', explode(',', $this->optionsRaw)) : null,
        ];

        if ($this->editingFieldId) {
            // Atualizar existente
            $this->category->fields()->findOrFail($this->editingFieldId)->update($data);
            $msg = 'Atributo atualizado!';
        } else {
            // Criar novo
            $maxOrder = $this->category->fields()->max('order') ?? 0;
            $data['order'] = $maxOrder + 1;
            $this->category->fields()->create($data);
            $msg = 'Campo acoplado com sucesso!';
        }

        $this->cancelEdit();
        session()->flash('ok', $msg);
    }

    public function usePreset($label, $type, $options = null)
    {
        $this->cancelEdit(); // Garante que sai do modo edição se clicar num preset
        $this->label = $label;
        $this->type = $type;
        $this->optionsRaw = $options;
    }

    public function updateOrder($items)
    {
        foreach ($items as $item) {
            CategoryField::where('id', $item['value'])->update(['order' => $item['order']]);
        }
    }

    public function removeField($id)
    {
        $this->category->fields()->findOrFail($id)->delete();
        if ($this->editingFieldId == $id) $this->cancelEdit();
    }

    public function render()
    {
        return view('livewire.category-fields', [
            'fields' => $this->category->fields()->orderBy('order')->get(),
            'types' => [
                'text' => 'Texto Simples',
                'number' => 'Número / Valor',
                'select' => 'Lista de Seleção',
                'date' => 'Data Específica',
                'checkbox' => 'Sim/Não (Check)',
            ]
        ]);
    }
}
