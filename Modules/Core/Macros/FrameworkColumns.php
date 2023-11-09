<?php

namespace Modules\Core\Macros;

use Illuminate\Database\Schema\Blueprint;
use Modules\Core\Contracts\DocStatus;

class FrameworkColumns
{
    public static function auditColumns(Blueprint $table): void
    {
        $table->foreignId('owner_id')->nullable()->constrained('users')->restrictOnDelete();
        $table->foreignId('modified_by')->nullable()->constrained('users')->nullOnDelete();
        $table->foreignId('submitted_by')->nullable()->constrained('users')->restrictOnDelete();
        $table->foreignId('cancelled_by')->nullable()->constrained('users')->restrictOnDelete();
        $table->foreignId('reversed_by')->nullable()->constrained('users')->nullOnDelete();


        $table->timestamp('submitted_at')->nullable();
        $table->timestamp('cancelled_at')->nullable();
        $table->timestamp('reversed_at')->nullable();
    }

    public static function docStatusColumn(Blueprint $table): void
    {
        $table->enum('doc_status', [DocStatus::DRAFT, DocStatus::SUBMITTED, DocStatus::CANCELLED])->default(DocStatus::DRAFT);
    }

    public static function activeStatusColumn(Blueprint $table): void
    {
        $table->boolean('is_active')->default(true);
    }


    public static function dropAuditColumns(Blueprint $table): void
    {
        $table->dropColumn('owner_id');
        $table->dropColumn('modified_by');
        $table->dropColumn('submitted_by');
        $table->dropColumn('cancelled_by');
        $table->dropColumn('reversed_by');
        $table->dropColumn('submitted_at');
        $table->dropColumn('cancelled_at');
        $table->dropColumn('reversed_at');
    }

    public static function dropDocStatusColumn(Blueprint $table): void
    {
        $table->dropColumn('doc_status');
    }

    public static function dropActiveStatusColumn(Blueprint $table): void
    {
        $table->dropColumn('is_active');
    }


    public static function teamColumn(Blueprint $table): void
    {
        $table->foreignId('team_id')->constrained('teams')->restrictOnDelete();
    }

    public static function dropTeamColumn(Blueprint $table): void
    {
        $table->dropColumn('team_id');
    }

    public static function codeColumn(Blueprint $table): void
    {
        $table->string('code', 50)->unique('unique_code_idx');
    }

    public static function dropCodeColumn(Blueprint $table): void
    {
        $table->dropUnique('unique_code_idx');
        $table->dropColumn('code');
    }

    public static function frameworkColumns(
        Blueprint $table,
        bool      $docStatus = true,
        bool      $activeStatus = true,
        bool      $audit = true,
        bool      $team = true,
        bool      $code = true
    ): void
    {
        if ($code) $table->codeColumn();
        if ($docStatus) $table->docStatusColumn();
        if ($activeStatus) $table->activeStatusColumn();
        if ($audit) $table->auditColumns();
        if ($team) $table->teamColumn();
    }

    public static function dropFrameworkColumns(
        Blueprint $table,
        bool      $docStatus = true,
        bool      $activeStatus = true,
        bool      $audit = true,
        bool      $team = true,
        bool      $code = true
    ): void
    {
        if ($team) $table->dropTeamColumn();
        if ($audit) $table->dropAuditColumns();
        if ($activeStatus) $table->dropActiveStatusColumn();
        if ($docStatus) $table->dropDocStatusColumn();
        if ($code) $table->dropCodeColumn();
    }

    public static function registerMacros(): void
    {
        Blueprint::macro('docStatusColumn', function () {
            FrameworkColumns::docStatusColumn($this);
        });

        Blueprint::macro('dropDocStatusColumn', function () {
            FrameworkColumns::dropDocStatusColumn($this);
        });

        Blueprint::macro('activeStatusColumn', function () {
            FrameworkColumns::activeStatusColumn($this);
        });

        Blueprint::macro('dropActiveStatusColumn', function () {
            FrameworkColumns::dropActiveStatusColumn($this);
        });


        Blueprint::macro('auditColumns', function () {
            FrameworkColumns::auditColumns($this);
        });
        Blueprint::macro('dropAuditColumns', function () {
            FrameworkColumns::dropAuditColumns($this);
        });

        Blueprint::macro('teamColumn', function () {
            FrameworkColumns::teamColumn($this);
        });
        Blueprint::macro('dropTeamColumn', function () {
            FrameworkColumns::dropTeamColumn($this);
        });

        Blueprint::macro('codeColumn', function () {
            FrameworkColumns::codeColumn($this);
        });

        Blueprint::macro('dropCodeColumn', function () {
            FrameworkColumns::dropCodeColumn($this);
        });

        Blueprint::macro('frameworkColumns', function (
            bool $docStatus = true,
            bool $activeStatus = true,
            bool $audit = true,
            bool $team = true,
            bool $code = true
        ) {
            FrameworkColumns::frameworkColumns(table: $this, docStatus: $docStatus, activeStatus: $activeStatus, audit: $audit, team: $team, code: $code);
        });

        Blueprint::macro('dropFrameworkColumns', function (
            bool $docStatus = true,
            bool $activeStatus = true,
            bool $audit = true,
            bool $team = true,
            bool $code = true
        ) {
            FrameworkColumns::dropFrameworkColumns(table: $this, docStatus: $docStatus, activeStatus: $activeStatus, audit: $audit, team: $team, code: $code);
        });
    }
}
