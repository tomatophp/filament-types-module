<?php

namespace Modules\TypesManager\Filament\Resources;

use TomatoPHP\FilamentIcons\Components\IconPicker;
use TomatoPHP\FilamentIcons\Components\IconColumn;
use Filament\Resources\Concerns\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Modules\TypesManager\Facades\FilamentTypes;
use Modules\TypesManager\Filament\Resources\TypeResource\Pages;
use Modules\TypesManager\Models\Type;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TypeResource extends Resource
{
    use Translatable;

    protected static ?string $model = Type::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getNavigationGroup(): ?string
    {
        return "Settings";
    }

    private static function getTypes(?string $getFor=null): array
    {
        $mergeTypes = [];
        $mergeFor = [];
        foreach (config('filament-types.types') as $key => $type){
            $mergeFor[$key] = $key;
            $mergeTypes[$key] = [];
        }

        foreach (FilamentTypes::getFor() as $for){
            $mergeFor[$for] = $for;

            $providerTypes = FilamentTypes::getTypes($for);
            foreach ($providerTypes as $key=>$type){
                $mergeTypes[$key] = [];
            }
        }
        foreach (config('filament-types.types') as $key => $type){
            foreach ($type as $item){
                if(!in_array($item, $mergeTypes[$key])) {
                    $mergeTypes[$key][$item] = $item;
                }
            }

        }
        foreach (FilamentTypes::getFor() as $for){
            $providerTypes = FilamentTypes::getTypes($for);
            foreach ($providerTypes as $key => $type){
                foreach ($type as $item){
                    if(!in_array($item, $mergeTypes[$key])){
                        $mergeTypes[$key][$item] = $item;
                    }
                }
            }
        }

        return !empty($getFor) ? collect($mergeTypes)->filter(fn($types, $key) => $key === $getFor)->toArray() : $mergeFor;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                    ->label(trans('filament-types::messages.form.image'))
                    ->columnSpan(2)
                    ->collection('image')
                    ->image()
                    ->maxFiles(1),
                Forms\Components\Select::make('for')
                    ->label(trans('filament-types::messages.form.for'))
                    ->options(static::getTypes())
                    ->searchable()
                    ->afterStateUpdated(function (Forms\Set $set){
                        $set('type', null);
                        $set('parent_id', null);
                    })
                    ->live()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label(trans('filament-types::messages.form.type'))
                    ->options(function(Forms\Get $get){
                        return $get('for') ? static::getTypes($get('for')) : [];
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('parent_id')
                    ->label(trans('filament-types::messages.form.parent_id'))
                    ->columnSpan(2)
                    ->options(fn(Forms\Get $get) => Type::whereNull('parent_id')
                        ->where('for', $get('for'))
                        ->where('type', $get('type'))
                        ->get()
                        ->pluck('name', 'id')
                        ->toArray()
                    )
                    ->searchable()
                    ->live(),
                Forms\Components\TextInput::make('name')
                    ->label(trans('filament-types::messages.form.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('key')
                    ->label(trans('filament-types::messages.form.key'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label(trans('filament-types::messages.form.description'))
                    ->columnSpanFull(),
                Forms\Components\ColorPicker::make('color')->columnSpan(2)->label(trans('filament-types::messages.form.color')),
                IconPicker::make('icon')->columnSpan(2)->label(trans('filament-types::messages.form.icon')),
                Forms\Components\Toggle::make('is_activated')->label(trans('filament-types::messages.form.is_activated')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('for')
                    ->label(trans('filament-types::messages.form.for'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(trans('filament-types::messages.form.type'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(trans('filament-types::messages.form.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('key')
                    ->label(trans('filament-types::messages.form.key'))
                    ->searchable(),
                Tables\Columns\ColorColumn::make('color')
                    ->label(trans('filament-types::messages.form.color'))
                    ->searchable(),
                IconColumn::make('icon')
                    ->label(trans('filament-types::messages.form.icon'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_activated')
                    ->label(trans('filament-types::messages.form.is_activated'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(trans('filament-types::messages.form.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(trans('filament-types::messages.form.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultGroup('for')
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\Select::make('for')
                            ->label(trans('filament-types::messages.form.for'))
                            ->options(static::getTypes())
                            ->searchable()
                            ->afterStateUpdated(function (Forms\Set $set){
                                $set('type', null);
                                $set('parent_id', null);
                            })
                            ->live(),
                        Forms\Components\Select::make('type')
                            ->label(trans('filament-types::messages.form.type'))
                            ->options(fn(Forms\Get $get) => $get('for') ? static::getTypes($get('for')) : [])
                            ->searchable(),
                        Forms\Components\Select::make('parent_id')
                            ->label(trans('filament-types::messages.form.parent_id'))
                            ->options(fn(Forms\Get $get) => Type::whereNull('parent_id')
                                ->where('for', $get('for'))
                                ->where('type', $get('type'))
                                ->get()
                                ->pluck('name', 'id')
                                ->toArray()
                            )
                            ->searchable()
                            ->live()
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if(isset($data['for']) && !empty($data['for']))
                            $query->where('for', $data['for']);
                        if(isset($data['type']) && !empty($data['type']))
                            $query->where('type', $data['type']);
                        if(isset($data['parent_id']) && !empty($data['parent_id']))
                            $query->where('parent_id', $data['parent_id']);

                        return $query;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTypes::route('/')
        ];
    }
}
