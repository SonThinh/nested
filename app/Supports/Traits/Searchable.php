<?php

namespace App\Supports\Traits;

use App\Supports\Observers\ElasticsearchObserver;
use Elasticsearch\Client;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait Searchable
 *
 * @mixin Model
 */
trait Searchable
{
    public static function bootSearchable()
    {
        if (config('elasticsearch.enable')) {
            static::observe(ElasticsearchObserver::class);
        }
    }

    public function elasticsearchIndex(Client $elasticsearchClient)
    {
        return $elasticsearchClient->index([
            'index' => $this->getSearchIndex(),
            'type'  => $this->getSearchType(),
            'id'    => $this->getKey(),
            'body'  => $this->toElasticsearchDocumentArray(),
        ]);
    }

    public function elasticsearchDelete(Client $elasticsearchClient)
    {
        return $elasticsearchClient->delete([
            'index' => $this->getTable(),
            'type'  => $this->getSearchType(),
            'id'    => $this->getKey(),
        ]);
    }

    public function toElasticsearchDocumentArray(): array
    {
        $searchFields = $this->getSearchFields();
        $result = [];
        foreach ($searchFields as $field) {
            $result[$field] = $this->attributes[$field] ?? '';
        }

        return $result;
    }

    abstract public function getSearchFields(): array;

    abstract public function getSearchIndex(): string;

    abstract public function getSearchType(): string;
}
