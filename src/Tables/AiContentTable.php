<?php

namespace DoanQuang\AiContent\Tables;

use DoanQuang\AiContent\Models\AiContent;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class AiContentTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(AiContent::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('ai-content.create'))
            ->addActions([
                EditAction::make()->route('ai-content.edit'),
                DeleteAction::make()->route('ai-content.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('ai-content.edit'),
                StatusColumn::make(),
                CreatedAtColumn::make(),
                
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('ai-content.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                StatusBulkChange::make(),                
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                $query->select([
                    'id',
                    'name',
                    'status',
                    'prompt_content',
                    'created_at',
                ]);
            });
    }
}
