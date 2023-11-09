<?php

namespace Modules\Core\Concerns\Model;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Contracts\DocStatus;
use Modules\Core\Models\DocumentCancellation;

trait HasDocStatus
{
    public static function bootHasDocStatus(): void
    {
        static::addGlobalScope('not-cancelled', function (Builder $builder) {
            $builder->where('doc_status', '!=', DocStatus::CANCELLED);
        });
        static::creating(function (Model $model) {
            if (! $model->doc_status) {
                $model->doc_status = DocStatus::DRAFT;
            }
        });
        static::updating(function (Model $model) {
            if (!$model->isDraft()) {
                throw new \RuntimeException('You can only update documents which are in draft mode.');
            }
        });

        static::deleting(function (Model $model) {
            if (!$model->isDraft()) {
                throw new \RuntimeException('You can only delete documents which are in draft mode.');
            }
        });
    }

    protected function initializeHasDocStatus(): void
    {
        $this->casts['is_active'] = 'bool';
    }

    public function scopeWhereDraft(Builder $builder): Builder
    {
        return $builder->where('doc_status', '=', DocStatus::DRAFT);
    }

    public function scopeWhereSubmitted(Builder $builder): Builder
    {
        return $builder->where('doc_status', '=', DocStatus::SUBMITTED);
    }

    public function scopeWhereCancelled(Builder $builder): Builder
    {
        return $builder->where('doc_status', '=', DocStatus::CANCELLED);
    }

    public function scopeWithCancelled(Builder $builder): Builder
    {
        return $builder->withoutGlobalScope('not-cancelled');
    }

    public function scopeOnlyCancelled(Builder $builder): Builder
    {
        return $builder->withoutGlobalScope('not-cancelled')->whereCancelled();
    }

    public function isDraft(): bool
    {
        return $this->doc_status === DocStatus::DRAFT;
    }

    public function isSubmitted(): bool
    {
        return $this->doc_status === DocStatus::SUBMITTED;
    }

    public function isCancelled(): bool
    {
        return $this->doc_status === DocStatus::CANCELLED;
    }

    public function isNotCancelled(): bool
    {
        return ! $this->isCancelled();
    }

    public function submit($onlyIfDraft = true): static
    {
        if ($onlyIfDraft && ! $this->isDraft()) {
            return $this;
        }
        if ($this->isDraft()) throw new \RuntimeException('Only Draft Documents can be Submitted.');
        $this->submitting();
        $this->doc_status = DocStatus::SUBMITTED;
        $this->submitted_by = auth()->id();
        $this->submitted_at = now();
        $this->saveQuietly();
        $this->submitted();
        return $this;
    }

    public function cancel(?string $reason = ''): static
    {
        if (!$this->isSubmitted()) throw new \RuntimeException('Only Submitted Documents can be Cancelled.');
        \DB::transaction(function () use ($reason) {
            $this->canceling($reason);
            $this->doc_status = DocStatus::CANCELLED;
            $this->cancelled_by = \Auth::id();
            $this->cancelled_at = now();
            $this->saveQuietly();
            // Create a Doc Cancellation log:
            /*$log = DocumentCancellation::create([
                'reason' => $reason,
                'document_code' => $this->code,
                'document_type' => $this->getMorphClass(),
                'document_id' => $this->id,
            ]);
            $log->submit();*/
            $this->cancelled($reason);
        });

        return $this;
    }

    public function submitting()
    {
        // Hook your logic here
    }

    public function submitted()
    {
        // Hook your logic here
    }

    public function canceling(?string $reason = '')
    {
        // Hook your logic here
    }

    public function cancelled(?string $reason = '')
    {
        // Hook your logic here.
    }
}
