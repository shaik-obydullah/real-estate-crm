<?php

namespace App\Http\Livewire\Tags;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Tag;

#[Layout('layouts.app', ['title' => 'Tags'])]
class Index extends Component
{
    public string $search = '';
    public bool $showCreateForm = false;
    public int $editId = 0;
    public string $formName = '';
    public string $formColor = '#3b82f6';

    public array $colorPresets = [
        '#3b82f6', '#22c55e', '#eab308', '#ef4444',
        '#a855f7', '#6366f1', '#ec4899', '#6b7280',
    ];

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editId = 0;
        $this->formName = '';
        $this->formColor = '#3b82f6';
    }

    public function openEdit(int $id)
    {
        $tag = Tag::findOrFail($id);
        $this->editId = $id;
        $this->formName = $tag->name;
        $this->formColor = $tag->color ?? '#3b82f6';
        $this->showCreateForm = true;
    }

    public function save()
    {
        $this->validate([
            'formName' => 'required|string|max:255|unique:tags,name,' . ($this->editId ?: ''),
            'formColor' => 'required|string',
        ]);

        if ($this->editId) {
            Tag::findOrFail($this->editId)->update([
                'name' => $this->formName,
                'color' => $this->formColor,
            ]);
            session()->flash('success', 'Tag updated.');
        } else {
            Tag::create([
                'name' => $this->formName,
                'color' => $this->formColor,
            ]);
            session()->flash('success', 'Tag created.');
        }

        $this->resetForm();
        $this->showCreateForm = false;
    }

    public function deleteTag(int $id)
    {
        Tag::where('id', $id)->delete();
        session()->flash('success', 'Tag deleted.');
    }

    public function render()
    {
        $tags = Tag::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->get()
            ->map(function ($tag) {
                $tag->usage_count = 0;
                try { $tag->usage_count += $tag->customers()->count(); } catch (\Exception $e) {}
                try { $tag->usage_count += $tag->contacts()->count(); } catch (\Exception $e) {}
                try { $tag->usage_count += $tag->leads()->count(); } catch (\Exception $e) {}
                try { $tag->usage_count += $tag->opportunities()->count(); } catch (\Exception $e) {}
                try { $tag->usage_count += $tag->tasks()->count(); } catch (\Exception $e) {}
                return $tag;
            });

        return view('livewire.tags.index', ['tags' => $tags]);
    }
}
