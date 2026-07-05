<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Carbon\Carbon;
use App\Models\Blog;
use App\Models\Company;
use App\Models\Page;

class GenerateSitemap extends Command
{
    protected $signature = 'app:generate-sitemap';

    protected $description = 'Generate dynamic sitemap for the website';

    public function handle()
    {
        $sitemap = Sitemap::create();

        // Home page
        $sitemap->add(Url::create('/')
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(1.0));

       
        // Static or CMS Pages (About, Contact, etc.)
        foreach (Page::all() as $page) {
            $sitemap->add(
                Url::create(url($page->slug))
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.6)
            );
        }

        // Save the sitemap file
        //$sitemap->writeToFile('/home/sbxq5x4949wo/public_html/findsanything.com/sitemap.xml');
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('✅ Sitemap generated successfully!');
    }
}
