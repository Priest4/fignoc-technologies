<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use App\Models\Post;
use App\Models\Product;
use App\Models\Service;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Response;

class SiteController extends Controller
{
    public function home()
    {
        $faqs = config('fignoc.faqs');

        return view('pages.home', [
            'services'     => Service::active()->get(),
            'products'     => Product::active()->get(),
            'featuredWork' => PortfolioItem::featured()->get(),
            'testimonials' => config('fignoc.testimonials', []),
            // Home shows a subset of the FAQ pool (brief §7.1).
            'faqs'         => collect($faqs)->only([0, 1, 2, 3, 7])->values()->all(),
        ]);
    }

    public function servicesIndex()
    {
        return view('pages.services.index', [
            'grouped' => Service::active()->get()->groupBy('category'),
        ]);
    }

    public function serviceShow(Service $service)
    {
        $relatedSlugs = data_get($service->detail, 'related', []);
        $related = Service::active()->whereIn('slug', $relatedSlugs)->get();

        return view('pages.services.show', [
            'service' => $service,
            'related' => $related,
        ]);
    }

    public function workIndex()
    {
        $work = PortfolioItem::active()->get();

        return view('pages.work.index', [
            'featured' => $work->where('is_featured', true)->values(),
            'rest'     => $work->where('is_featured', false)->values(),
        ]);
    }

    public function workShow(PortfolioItem $work)
    {
        $product = $work->product_slug
            ? Product::where('slug', $work->product_slug)->first()
            : null;

        return view('pages.work.show', [
            'work'    => $work,
            'product' => $product,
        ]);
    }

    public function productsIndex()
    {
        return view('pages.products.index', [
            'products' => Product::active()->get(),
        ]);
    }

    public function productShow(Product $product)
    {
        $work = $product->work_slug
            ? PortfolioItem::where('slug', $product->work_slug)->first()
            : null;

        return view('pages.products.show', [
            'product' => $product,
            'work'    => $work,
        ]);
    }

    public function about()
    {
        return view('pages.about', [
            'team' => TeamMember::active()->get(),
        ]);
    }

    public function contact()
    {
        return view('pages.contact', [
            'faqs' => config('fignoc.faqs'),
        ]);
    }

    public function insightsIndex()
    {
        $posts = Post::published()->get();

        return view('pages.insights.index', [
            'featured' => $posts->first(),
            'posts' => $posts->slice(1)->values(),
            'all' => $posts,
        ]);
    }

    public function insightShow(Post $insight)
    {
        abort_unless($insight->is_published, 404);

        return view('pages.insights.show', [
            'post' => $insight,
            'related' => Post::published()
                ->where('id', '!=', $insight->id)
                ->take(3)
                ->get(),
        ]);
    }

    public function privacy()
    {
        return view('pages.legal.privacy');
    }

    public function terms()
    {
        return view('pages.legal.terms');
    }

    public function sitemap()
    {
        $today = now()->toDateString();

        $urls = collect([
            ['name' => 'home', 'priority' => '1.0'],
            ['name' => 'services', 'priority' => '0.9'],
            ['name' => 'work', 'priority' => '0.8'],
            ['name' => 'products', 'priority' => '0.8'],
            ['name' => 'about', 'priority' => '0.7'],
            ['name' => 'contact', 'priority' => '0.8'],
            ['name' => 'insights', 'priority' => '0.7'],
            ['name' => 'privacy', 'priority' => '0.3'],
            ['name' => 'terms', 'priority' => '0.3'],
        ])->map(fn ($row) => [
            'loc' => route($row['name']),
            'priority' => $row['priority'],
            'lastmod' => $today,
        ]);

        foreach (Service::active()->get() as $s) {
            $urls->push([
                'loc' => route('services.show', $s),
                'priority' => $s->is_featured ? '0.9' : '0.6',
                'lastmod' => optional($s->updated_at)->toDateString() ?? $today,
            ]);
        }
        foreach (PortfolioItem::active()->get() as $w) {
            $urls->push([
                'loc' => route('work.show', $w),
                'priority' => '0.6',
                'lastmod' => optional($w->updated_at)->toDateString() ?? $today,
            ]);
        }
        foreach (Product::active()->get() as $p) {
            $urls->push([
                'loc' => route('products.show', $p),
                'priority' => '0.7',
                'lastmod' => optional($p->updated_at)->toDateString() ?? $today,
            ]);
        }
        foreach (Post::published()->get() as $post) {
            $urls->push([
                'loc' => route('insights.show', $post),
                'priority' => '0.5',
                'lastmod' => optional($post->updated_at ?? $post->published_at)->toDateString() ?? $today,
            ]);
        }

        return Response::view('sitemap', ['urls' => $urls], 200)
            ->header('Content-Type', 'application/xml');
    }
}
