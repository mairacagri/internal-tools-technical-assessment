<?php

namespace App\Jobs\Ingestors\CrowdOx;

use App\Ingestors\CrowdOx\CrowdOxCustomerIngestor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IngestCrowdOxCustomers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $page_start = 1;
    protected $pages_to_fetch = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($page_start = null, $pages_to_fetch = null)
    {
        if ($page_start !== null) { $this->page_start = $page_start; }
        if ($pages_to_fetch !== null) { $this->pages_to_fetch = $pages_to_fetch; }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ingestor = new CrowdOxCustomerIngestor(null, $this->page_start, $this->pages_to_fetch);
        $ingest = $ingestor->ingest();

        if ($ingest->resourceExhausted() == false) {
            IngestCrowdOxCustomers::dispatch($this->page_start + $this->pages_to_fetch, $this->pages_to_fetch);
        }
    }
}
