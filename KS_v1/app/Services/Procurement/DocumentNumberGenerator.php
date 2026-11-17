<?php

namespace App\Services\Procurement;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DocumentNumberGenerator
{
    /**
     * Allocate the next sequential document number: PREFIX-0001.
     * Supports legacy date-bucket numbers (e.g. PR-20260720-0001) when computing the next sequence.
     */
    public function next(string $prefixKey, string $modelClass, string $column): string
    {
        $prefixRoot = strtoupper((string) config("procurement.document_prefixes.{$prefixKey}", strtoupper($prefixKey)));
        $padding = (int) config('procurement.document_sequence_padding', 4);
        $lockName = 'procurement_doc_'.$prefixKey;

        return DB::transaction(function () use ($prefixRoot, $padding, $modelClass, $column, $lockName) {
            $this->acquireLock($lockName);

            try {
                return $this->nextAvailableNumber($prefixRoot, $padding, $modelClass, $column);
            } finally {
                $this->releaseLock($lockName);
            }
        });
    }

    /**
     * Reserve the next document number and run $callback while the lock is held.
     * Use this when persisting the record in the same critical section to avoid duplicates.
     */
    public function allocate(string $prefixKey, string $modelClass, string $column, callable $callback): mixed
    {
        $prefixRoot = strtoupper((string) config("procurement.document_prefixes.{$prefixKey}", strtoupper($prefixKey)));
        $padding = (int) config('procurement.document_sequence_padding', 4);
        $lockName = 'procurement_doc_'.$prefixKey;

        return DB::transaction(function () use ($prefixRoot, $padding, $modelClass, $column, $lockName, $callback) {
            $this->acquireLock($lockName);

            try {
                $number = $this->nextAvailableNumber($prefixRoot, $padding, $modelClass, $column);

                return $callback($number);
            } finally {
                $this->releaseLock($lockName);
            }
        });
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function nextAvailableNumber(string $prefixRoot, int $padding, string $modelClass, string $column): string
    {
        $nextSeq = $this->nextSequence($prefixRoot, $modelClass, $column) + 1;

        do {
            $number = $prefixRoot.'-'.str_pad((string) $nextSeq, $padding, '0', STR_PAD_LEFT);
            $nextSeq++;
        } while ($this->numberExists($modelClass, $column, $number));

        return $number;
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function nextSequence(string $prefixRoot, string $modelClass, string $column): int
    {
        /** @var Builder $query */
        $query = $this->includesTrashedRecords($modelClass)
            ? $modelClass::withTrashed()->where($column, 'like', $prefixRoot.'-%')
            : $modelClass::query()->where($column, 'like', $prefixRoot.'-%');

        $codes = $query
            ->lockForUpdate()
            ->pluck($column);

        $max = 0;
        $pattern = '/^'.preg_quote($prefixRoot, '/').'-(?:\d{6,8}-)?(\d+)$/i';

        foreach ($codes as $code) {
            if (preg_match($pattern, (string) $code, $matches)) {
                $max = max($max, (int) $matches[1]);
            }
        }

        return $max;
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function numberExists(string $modelClass, string $column, string $number): bool
    {
        $query = $this->includesTrashedRecords($modelClass)
            ? $modelClass::withTrashed()->where($column, $number)
            : $modelClass::query()->where($column, $number);

        return $query->exists();
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function includesTrashedRecords(string $modelClass): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($modelClass), true);
    }

    private function acquireLock(string $lockName): void
    {
        $result = DB::selectOne('SELECT GET_LOCK(?, 10) AS locked', [$lockName]);
        $locked = (int) ($result->locked ?? 0);

        if ($locked !== 1) {
            throw new Exceptions\ProcurementException(
                "Could not acquire document number lock [{$lockName}]."
            );
        }
    }

    private function releaseLock(string $lockName): void
    {
        DB::selectOne('SELECT RELEASE_LOCK(?) AS released', [$lockName]);
    }
}
