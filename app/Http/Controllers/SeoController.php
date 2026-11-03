<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = array_map(
            fn (string $routeName): string => $this->publicUrl($routeName),
            ['home', 'projects', 'investment'],
        );

        return response()->view('sitemap', ['urls' => $urls], 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    public function robots(): Response
    {
        return response("User-agent: *\nAllow: /\n\nSitemap: ".$this->publicUrl('sitemap')."\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    private function publicUrl(string $routeName): string
    {
        return rtrim(config('app.url'), '/').route($routeName, [], false);
    }
}
