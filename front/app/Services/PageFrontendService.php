<?php

namespace App\Services;

use App\Models\Page;

class PageFrontendService
{
    public function show($locale, $slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        if ($slug === 'faq') {
            return $this->showFaq($page);
        }

        return view('pages.show', compact('page'));
    }

    public function index()
    {
        $pages = Page::where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.index', compact('pages'));
    }

    public function getMenuPages()
    {
        return Page::where('is_active', true)
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function showFaq($page)
    {
        $locale = app()->getLocale();
        $description = $locale === 'ar' ? $page->description_ar : $page->description_en;
        $faqItems = $this->parseFaqContent($description);

        return view('pages.faq', compact('page', 'faqItems'));
    }

    private function parseFaqContent($content)
    {
        $faqItems = [];

        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/&nbsp;/', ' ', $content);

        $parts = preg_split('/(<strong>.*?<\/strong>)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        for ($index = 1; $index < count($parts); $index += 2) {
            if (isset($parts[$index]) && isset($parts[$index + 1])) {
                $question = strip_tags($parts[$index]);
                $answer = $parts[$index + 1];

                $answer = $this->cleanHtmlContent($answer);

                if (!empty($question) && !empty($answer)) {
                    $faqItems[] = [
                        'question' => trim($question),
                        'answer' => $answer,
                    ];
                }
            }
        }

        return $faqItems;
    }

    private function cleanHtmlContent($content)
    {
        $content = preg_replace('/<p>\s*&nbsp;\s*<\/p>/i', '', $content);
        $content = preg_replace('/<p>\s*<\/p>/i', '', $content);

        $content = $this->fixMalformedHtmlTags($content);

        try {
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);

            $wrappedContent = '<div>' . $content . '</div>';
            $dom->loadHTML('<?xml encoding="UTF-8">' . $wrappedContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            $div = $dom->getElementsByTagName('div')->item(0);
            if ($div) {
                $content = '';
                foreach ($div->childNodes as $child) {
                    $content .= $dom->saveHTML($child);
                }
            }

            libxml_clear_errors();
        } catch (\Exception $e) {
            $content = $this->fixMalformedHtmlTags($content);
        }

        if (preg_match('/<li>/i', $content) && !preg_match('/<\/[uo]l>/i', $content)) {
            if (preg_match('/<ol/i', $content)) {
                $content .= '</ol>';
            } else {
                $content .= '</ul>';
            }
        }

        $content = preg_replace('/<br\s*\/?>\s*(<li>)/i', '$1', $content);
        $content = preg_replace('/(<\/li>)\s*<br\s*\/?>\s*(<li>)/i', '$1$2', $content);

        $content = preg_replace('/\s{2,}/', ' ', $content);
        $content = trim($content);

        return $content;
    }

    private function fixMalformedHtmlTags($content)
    {
        $content = preg_replace('/^(ul>)/i', '<ul>', $content);
        $content = preg_replace('/^(ol>)/i', '<ol>', $content);
        $content = preg_replace('/^(li>)/i', '<li>', $content);
        $content = preg_replace('/^(p>)/i', '<p>', $content);
        $content = preg_replace('/^(div>)/i', '<div>', $content);
        $content = preg_replace('/^(span>)/i', '<span>', $content);
        $content = preg_replace('/^(strong>)/i', '<strong>', $content);
        $content = preg_replace('/^(em>)/i', '<em>', $content);
        $content = preg_replace('/^(b>)/i', '<b>', $content);
        $content = preg_replace('/^(i>)/i', '<i>', $content);

        $content = preg_replace('/<\/ol(?!>)/', '</ol>', $content);
        $content = preg_replace('/<\/ul(?!>)/', '</ul>', $content);
        $content = preg_replace('/<\/li(?!>)/', '</li>', $content);
        $content = preg_replace('/<\/p(?!>)/', '</p>', $content);
        $content = preg_replace('/<\/div(?!>)/', '</div>', $content);
        $content = preg_replace('/<\/span(?!>)/', '</span>', $content);
        $content = preg_replace('/<\/strong(?!>)/', '</strong>', $content);
        $content = preg_replace('/<\/em(?!>)/', '</em>', $content);
        $content = preg_replace('/<\/b(?!>)/', '</b>', $content);
        $content = preg_replace('/<\/i(?!>)/', '</i>', $content);

        $content = preg_replace('/<\/ol$/i', '</ol>', $content);
        $content = preg_replace('/<\/ul$/i', '</ul>', $content);
        $content = preg_replace('/<\/li$/i', '</li>', $content);
        $content = preg_replace('/<\/p$/i', '</p>', $content);
        $content = preg_replace('/<\/div$/i', '</div>', $content);
        $content = preg_replace('/<\/span$/i', '</span>', $content);
        $content = preg_replace('/<\/strong$/i', '</strong>', $content);
        $content = preg_replace('/<\/em$/i', '</em>', $content);
        $content = preg_replace('/<\/b$/i', '</b>', $content);
        $content = preg_replace('/<\/i$/i', '</i>', $content);

        $content = preg_replace('/<\/ol(?!>)/', '</ol>', $content);
        $content = preg_replace('/<\/ul(?!>)/', '</ul>', $content);
        $content = preg_replace('/<\/li(?!>)/', '</li>', $content);
        $content = preg_replace('/<\/p(?!>)/', '</p>', $content);
        $content = preg_replace('/<\/div(?!>)/', '</div>', $content);
        $content = preg_replace('/<\/span(?!>)/', '</span>', $content);
        $content = preg_replace('/<\/strong(?!>)/', '</strong>', $content);
        $content = preg_replace('/<\/em(?!>)/', '</em>', $content);
        $content = preg_replace('/<\/b(?!>)/', '</b>', $content);
        $content = preg_replace('/<\/i(?!>)/', '</i>', $content);

        $content = preg_replace('/<br\/(?!>)/', '<br/>', $content);
        $content = preg_replace('/<hr\/(?!>)/', '<hr/>', $content);
        $content = preg_replace('/<img\/(?!>)/', '<img/>', $content);
        $content = preg_replace('/<input\/(?!>)/', '<input/>', $content);

        return $content;
    }
}
