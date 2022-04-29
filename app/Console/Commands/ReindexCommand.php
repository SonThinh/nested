<?php

namespace App\Console\Commands;

use App\Enums\ProposalApproveStatusEnum;
use App\Models\Category;
use App\Models\Proposal;
use App\Models\User;
use App\Services\P3Service;
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

        $this->removeRecipeFromIndex(new User());
        $this->analyzer(new User());
        $this->info('Indexing all user. This might take a while...');
        $this->processData(User::cursor());

        $this->info("\n");

        $this->info('Indexing all category. This might take a while...');
        $this->removeRecipeFromIndex(new Category());
        $this->analyzer(new Category());
        $this->processData(Category::cursor());

        $this->info("\nDone!");
    }

    public function processData($data)
    {
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

    public function putPipeline()
    {
        $params = [
            'id'   => 'attachment',
            'body' => [
                'description' => 'Extract attachment information',
                'processors'  => [
                    [
                        'attachment' => [
                            'field' => 'proposal_data',
                        ],
                        'remove'     => [
                            'field' => 'proposal_data',
                        ],
                    ],
                ],
            ],
        ];
        $this->elasticsearch->ingest()->putPipeline($params);
    }
}
