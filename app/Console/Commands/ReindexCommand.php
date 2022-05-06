<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\User;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Console\Command;

class ReindexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:reindex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes all articles to Elasticsearch';

    /** @var \Elasticsearch\Client */
    private $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        parent::__construct();

        $this->elasticsearch = $elasticsearch;
    }

    public function handle()
    {
        $this->info('Remove all indexes....');
        $this->removeAllIndex();

        $this->info('Indexing all user. This might take a while...');
        $this->processData(new User(), User::cursor());

        $this->info("\n");

        $this->info('Indexing all category. This might take a while...');
        $this->processData(new Category(), Category::cursor());

        $this->info("\nDone!");
    }

    public function processData($model, $data)
    {
        $this->removeRecipeFromIndex($model);
        $this->analyzer($model);
        $bar = $this->output->createProgressBar(count($data));

        $bar->start();

        foreach ($data as $item) {
            $body = $item->prepareRelationData();
            //reindex data
            $this->reindexData($item, $body);
            $bar->advance();
        }

        $bar->finish();
    }

    public function reindexData($model, $body)
    {
        $this->elasticsearch->index([
            'index' => $model->getSearchIndex(),
            'type'  => $model->getSearchType(),
            'id'    => $model->getKey(),
            'body'  => $body,
        ]);
    }

    public function removeRecipeFromIndex($model)
    {
        try {
            $this->elasticsearch->indices()->delete([
                'index' => $model->getSearchIndex(),
            ]);
        } catch (Missing404Exception $e) {
        }
    }

    public function removeAllIndex()
    {
        $client = new \GuzzleHttp\Client();
        $uri = 'http://localhost:9200/_all';
        try {
            $client->request('DELETE', $uri);
        } catch (Missing404Exception $e) {
        }
    }

    public function analyzer($model)
    {
        $params = [
            'index' => $model->getSearchIndex(),
            'body'  => array_filter([
                'settings' => $model->settings(),
                'mappings' => $model->mapping(),
            ]),
        ];

        $this->elasticsearch->indices()->create($params);
    }

    //public function putPipeline()
    //{
    //    $params = [
    //        'id'   => 'attachment',
    //        'body' => [
    //            'description' => 'Extract attachment information',
    //            'processors'  => [
    //                [
    //                    'attachment' => [
    //                        'field' => 'proposal_data',
    //                    ],
    //                    'remove'     => [
    //                        'field' => 'proposal_data',
    //                    ],
    //                ],
    //            ],
    //        ],
    //    ];
    //    $this->elasticsearch->ingest()->putPipeline($params);
    //}
}
