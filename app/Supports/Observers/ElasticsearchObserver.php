<?php

namespace App\Supports\Observers;

use App\Jobs\UpdateOnElasticsearchJob;
use Elasticsearch\Client;

class ElasticsearchObserver {
    private Client $elasticsearchClient;

    public function __construct(Client $elasticsearchClient) {
        $this->elasticsearchClient = $elasticsearchClient;
    }

    public function saved($model) {
        // may we need to put it to queue
        UpdateOnElasticsearchJob::dispatch($model)->onQueue('high');
    }

    public function deleted($model) {
        $model->elasticSearchDelete($this->elasticsearchClient);
    }
}
