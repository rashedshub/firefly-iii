<?php

/**
 * TransactionJournalMeta.php
 * Copyright (c) 2019 james@firefly-iii.org
 *
 * This file is part of Firefly III (https://github.com/firefly-iii).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace FireflyIII\Models;

use FireflyIII\Support\Models\ReturnsIntegerIdTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use function Safe\json_decode;
use function Safe\json_encode;

class TransactionJournalMeta extends Model
{
    use ReturnsIntegerIdTrait;
    use SoftDeletes;

    protected $casts
                        = [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

    protected $fillable = ['transaction_journal_id', 'name', 'data', 'hash'];

    protected $table    = 'journal_meta';

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function getDataAttribute($value)
    {
        return json_decode((string) $value, false);
    }

    /**
     * @param mixed $value
     */
    public function setDataAttribute($value): void
    {
        $data                     = json_encode($value);
        $this->attributes['data'] = $data;
        $this->attributes['hash'] = hash('sha256', (string) $data);
    }

    public function transactionJournal(): BelongsTo
    {
        return $this->belongsTo(TransactionJournal::class);
    }

    protected function transactionJournalId(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => (int) $value,
        );
    }
}
