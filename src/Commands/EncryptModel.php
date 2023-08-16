<?php

namespace Zaidysf\EloquentDatabaseEncryption\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EncryptModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eloquent-encryption:encrypt-model {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt model\'s rows';

    private array $attributes = [];

    private Model $model;

    /**
     * Execute the console command.
     *
     * @throws Exception
     */
    public function handle(): int
    {
        $class = $this->argument('model');
        $this->model = $this->guardClass($class);
        $this->attributes = $this->model->getEncryptableAttributes();
        $table = $this->model->getTable();
        $primaryKeyID = $this->model->getKeyName();
        $total = $this->model->where('encrypted', 0)->count();
        $this->model::$enableEncryption = false;

        if ($total > 0) {
            $this->comment($total.' records will be encrypted');
            $bar = $this->output->createProgressBar($total);
            $bar->setFormat('%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

            $records = $this->model->orderBy($primaryKeyID, 'asc')->where('encrypted', 0)
                ->chunkById(100, function ($records) use ($table, $bar, $primaryKeyID) {
                    foreach ($records as $record) {
                        $record->timestamps = false;
                        $attributes = $this->getEncryptedAttributes($record);

                        $updateID = "{$record->{$primaryKeyID}}";
                        DB::table($table)->where($primaryKeyID, $updateID)->update($attributes);
                        $bar->advance();
                        $record = null;
                        $attributes = null;
                    }
                });

            $bar->finish();
        }

        $this->comment($class.' has been encrypted');

        return self::SUCCESS;
    }

    /**
     * @throws Exception
     */
    public function guardClass($class): Model
    {
        if (! class_exists($class)) {
            throw new Exception("Class {$class} does not exists");
        }
        $model = new $class();
        $this->validateHasEncryptedColumn($model);

        return $model;
    }

    private function validateHasEncryptedColumn($model): void
    {
        $table = $model->getTable();
        if (! Schema::hasColumn($table, 'encrypted')) {
            $this->comment('Creating encrypted column');
            Schema::table($table, function (Blueprint $table) {
                $table->tinyInteger('encrypted')->default(0);
            });
        }
    }

    private function getEncryptedAttributes($record): array
    {
        $encryptedFields = ['encrypted' => 1];

        foreach ($this->attributes as $attribute) {
            $raw = $record->getOriginal($attribute);
            $encryptedFields[$attribute] = $this->model->encryptAttribute($raw);
        }

        return $encryptedFields;
    }
}
