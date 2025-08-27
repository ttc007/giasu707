<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate sitemap.xml for SEO';

    public function handle()
    {
        $this->info('🔄 Đang tạo sitemap...');

        SitemapGenerator::create(config('app.url'))
            ->hasCrawled(function (Url $url) {
                $path = $url->path();

                // Loại bỏ các route không nên index
                if (
                    str_starts_with($path, 'admin') ||
                    str_starts_with($path, 'login') ||
                    str_starts_with($path, 'dang-nhap') ||
                    str_starts_with($path, 'dang-ki') ||
                    str_starts_with($path, 'dang-xuat') ||
                    str_starts_with($path, 'api') ||
                    str_starts_with($path, 'upload') ||
                    str_starts_with($path, 'trang-ca-nhan') ||
                    str_starts_with($path, 'cap-nhat-trang-ca-nhan') ||
                    str_starts_with($path, 'kich-hoat-tai-khoan') 
                ) {
                    return false;
                }

                return $url;
            })
            ->writeToFile(public_path('sitemap.xml'));

        $this->info('✅ sitemap.xml đã được tạo tại public/sitemap.xml');
    }
}
