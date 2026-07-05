<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TranslateController extends Controller
{
    public function translate(Request $request)
    {
        $text = $request->text;
        $source = $request->source ?? 'auto';
        $target = $request->target ?? 'hi';

        if (!$text) {
            return response()->json(['error' => 'No text provided'], 400);
        }

        try {

            /* ===============================
               HANDLE ARRAY
            ================================ */
            if (is_array($text)) {

                $translatedResults = [];

                foreach ($text as $t) {
                    $translatedResults[] = $this->translateHtml($t, $source, $target);
                }

                return response()->json([
                    'translated' => $translatedResults
                ]);
            }

            /* ===============================
               HANDLE SINGLE TEXT
            ================================ */
            $translated = $this->translateHtml($text, $source, $target);

            return response()->json([
                'translated' => $translated
            ]);

        } catch (\Exception $e) {

            \Log::error('TRANSLATE ERROR: ' . $e->getMessage());

            return response()->json([
                'error' => 'Translation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    private function translateHtml($html, $source, $target)
    {
        $dom = new \DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

        $xpath = new \DOMXPath($dom);

        foreach ($xpath->query('//text()') as $node) {

            $originalText = trim($node->nodeValue);

            if ($originalText === '')
                continue;

            try {
                $response = Http::get(
                    'https://translate.googleapis.com/translate_a/single',
                    [
                        'client' => 'gtx',
                        'sl' => $source,
                        'tl' => $target,
                        'dt' => 't',
                        'q' => $originalText
                    ]
                );

                if ($response->successful()) {

                    $body = $response->json();

                    if (isset($body[0])) {
                        $translated = collect($body[0])->pluck(0)->implode('');
                        $node->nodeValue = $translated;
                    }
                }

            } catch (\Exception $e) {
                \Log::error('NODE TRANSLATION ERROR: ' . $e->getMessage());
            }
        }

        // 🔥 CLEAN EXTRA TAGS
        $html = $dom->saveHTML();
        $html = preg_replace('/^<!DOCTYPE.+?>/', '', $html);
        $html = str_replace(['<html>', '</html>', '<body>', '</body>'], '', $html);

        return $html;
    }
}