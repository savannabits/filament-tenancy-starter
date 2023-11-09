<?php

namespace Modules\Core\Concerns\Policy;

use App\Models\User;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Helpers\Framework;

trait InheritsStandardPolicy
{
    abstract public function getResourceClass(): string;

    public function getSuffix(): string
    {
        return FilamentShield::getPermissionIdentifier($this->getResourceClass());
    }

    public function makeSuffixFromModel(Model | string $model): string
    {
        if (is_string($model)) {
            $class = $model::getModel()->getMorphClass();
        } else {
            $class = $model->getMorphClass();
        }

        return \Str::of(\Str::of($class)->explode('\\')->last() ?? '')
            ->snake('::')->toString();
    }

    public function viewAny(User $user): bool
    {
        return $user->can("view_any_{$this->getSuffix()}");
    }

    public function view(User $user, Model $model): bool
    {
        return $user->can("view_{$this->getSuffix()}");
    }

    public function create(User $user): bool
    {
        return $user->can("create_{$this->getSuffix()}");
    }

    public function update(User $user, Model $model): bool
    {
        return !$this->isImmutable() && $user->can("update_{$this->getSuffix()}") && (! Framework::model_has_doc_status($model) || $model->isDraft());
    }

    public function deleteAny(User $user): bool
    {
        return !$this->isImmutable() && $user->can("delete_any_{$this->getSuffix()}");
    }

    public function delete(User $user, Model $model)
    {
        return !$this->isImmutable() && $user->can("delete_{$this->getSuffix()}") && (! Framework::model_has_doc_status($model) || $model->isDraft());
    }

    public function submit(User $user, Model $model): bool
    {
        return  $user->can($this->perm('submit')) && Framework::model_has_doc_status($model) && $model->isDraft();
    }

    public function cancel(User $user, Model $model): bool
    {
        return !$this->isImmutable() && $user->can($this->perm('cancel')) && Framework::model_has_doc_status($model) && $model->isSubmitted();
    }

    public function reverse(User $user, Model $model): bool
    {
        return $user->can($this->perm('reverse')) && Framework::model_has_doc_status($model) && $model->isSubmitted();
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, Model $model): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user)
    {
        return $user->can("delete_any_{$this->getSuffix()}");
    }

    public function forceDelete(User $user, Model $model)
    {
        return $user->can("delete_{$this->getSuffix()}") && (! Framework::model_has_doc_status($model) || $model->isDraft());
    }
    public function isImmutable(): bool
    {
        $model = $this->getResourceClass()::getModel();
        return method_exists($model,'hasImmutableTrait');
    }

    public function perm(string $prefix): string
    {
        return "{$prefix}_{$this->getSuffix()}";
    }
}
