<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            $model->recordAudit('create', [], $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $diff = $model->auditDiff();

            if (empty($diff)) {
                return;
            }

            $model->recordAudit('update', $diff['old'], $diff['new']);
        });

        static::deleted(function (Model $model) {
            $model->recordAudit('delete', $model->getAttributes(), []);
        });
    }

    protected function auditDiff(): array
    {
        $old = [];
        $new = [];

        foreach ($this->getChanges() as $field => $value) {
            if ($field === 'updated_at') {
                continue;
            }

            $old[$field] = $this->getOriginal($field);
            $new[$field] = $value;
        }

        return $old ? ['old' => $old, 'new' => $new] : [];
    }

    protected function recordAudit(string $action, array $old, array $new): void
    {
        if (app()->runningInConsole() && ! app()->runningUnitTests()) {
            return;
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => static::class,
            'entity_id' => $this->getKey(),
            'old_values' => $old ?: null,
            'new_values' => $new ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
